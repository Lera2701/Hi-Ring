$(document).ready(function () {

    $('#lang').click(function (e) {
        if(window.location.href.indexOf('ua') > 0) {
            window.location.href = window.location.href.substr(0, window.location.href.indexOf('ua')) + window.location.href.substr(window.location.href.indexOf('ua') + 3, window.location.href.length);
        } else {
            window.location.href = window.location.href.substr(0, window.location.href.indexOf('localhost') + 9) + '/ua/' + window.location.href.substr(17, window.location.href.length);
        }
    });

    $('#submit').click(function (e) {
        e.preventDefault();
        let user = document.querySelector('#user').value;
        let pass = document.querySelector('#pass').value;
        $.ajax({
            type: 'POST',
            url: '../models/ajax.php',
            data: {'user': user, 'pass': pass},
            success: function (msg) {
                if (JSON.parse(msg).status === 'authorized') {
                    HideError();
                    /*document.cookie = "rights=" + JSON.parse(msg).rights + ";path=/;";
                    document.cookie = "id=" + JSON.parse(msg).id + ";path=/;";*/
                    window.location.href = '../views/main.php';
                } else if (JSON.parse(msg).status === 'wrong password' || JSON.parse(msg).status === 'no such login') {
                    ShowError('неправильний логін та/чи пароль. перевірте, будь ласка, та повторіть');
                } else if (JSON.parse(msg).status === 'error') {
                    ShowError('виникла помилка, повторіть, будь ласка');
                }
            }
        });
    });

    function ShowError(text) {
        let error = document.querySelector('#error');
        if (error && text.length) {
            error.innerHTML = text;
            error.style.display = 'block';
        }
    }

    function HideError() {
        let error = document.querySelector('#error');
        if (error) {
            error.style.display = 'block';
        }
    }

    function ShowNotification(text) {
        let note = document.createElement('div');
        note.className = 'notification';
        if (text === 'callback')
            note.innerHTML = 'Дякуємо! Ми зв\'яжемось із Вами найбижчим часом.<br><br>(натисність будь-де, щоб закрити)';
        else
            note.innerHTML = text;
        document.body.append(note);
        document.body.style.pointerEvents = 'none';
        document.body.style.overflow = 'hidden';
    }

    $('.navNote').click(function (e) {
        e.preventDefault();

        $.ajax({
            type: 'POST',
            url: '../models/ajax.php',
            data: {'action': 'getNotes', 'id': document.querySelector('.navNote').dataset.id},
            success: function (msg) {
                if (JSON.parse(msg).status === 'true') {
                    let str = '';
                    str += '<br>Поточні роботи:<br><br>';
                    let i = 0;

                    while(i < JSON.parse(msg).start.length) {
                        str += '<br>Локація: ' + JSON.parse(msg).location[i];
                        str += '<br>Початок: ' + JSON.parse(msg).start[i];
                        str += '<br>Кінець: ' + JSON.parse(msg).end[i] + '<br><br>';
                        i++;
                    }
                    ShowNotification(str);
                } else {
                    ShowNotification('Наразі немає повідомлень для Вас');
                }
            }
        });
    });

    function HideNotification() {
        let note = document.querySelector('.notification');
        if (note) {
            note.remove();
            document.body.style.pointerEvents = 'all';
            document.body.style.overflow = 'auto';
        }
    }
    $('#submitCallback').click(function (e) {
        e.preventDefault();
        ShowNotification('callback');
    });

    $(document).click(function (e) {
        if(document.querySelector('.notification') !== null && e.target.id !== 'submitCallback' && !e.target.classList.contains('navNote')) {
            HideNotification();
        }
    });



    $('#submitReg').click(function (e) {
        e.preventDefault();
        let user = document.querySelector('#userReg').value;
        let pass = document.querySelector('#passReg').value;
        let passRepeat = document.querySelector('#passReg1').value;
        if ((pass.length < 6 || passRepeat.length < 6) && user !== 'admin') {
            ShowError('Пароль має бути не коротше 6 символів');
        } else {
            HideError();

            $.ajax({
                type: 'POST',
                url: '../models/ajax.php',
                data: {'userReg': user, 'passReg': pass, 'passReg1': passRepeat},
                success: function (msg) {
                    if (JSON.parse(msg).status === 'registered') {
                        HideError();
                        /*document.cookie = "rights=0" + ";path=/;";
                        document.cookie = "id=" + JSON.parse(msg).id + ";path=/;";*/
                        window.location.href = '../views/main.php';
                    } else if (JSON.parse(msg).status === 'insert error') {
                        ShowError('виникла помилка, повторіть, будь ласка');
                    } else if (JSON.parse(msg).status === 'different passwords') {
                        ShowError('паролі не співпадають. перевірте дані та повторіть, будь ласка');
                    }
                }
            });
        }
    });
});