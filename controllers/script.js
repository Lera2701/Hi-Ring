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
                    /*document.cookie = "rights=" + JSON.parse(msg).rights  + ";path=/;";
                    document.cookie = "id=" + JSON.parse(msg).id + ";path=/;";*/
                    window.location.href = '../views/main.php';
                } else if (JSON.parse(msg).status === 'wrong password' || JSON.parse(msg).status === 'no such login') {
                    ShowError('Wrong login or/and password. Check and retry');
                } else if (JSON.parse(msg).status === 'error') {
                    ShowError('Error occurred, please, try again');
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
        let note = document.querySelector('.notification');
        if (note) {
            if (text === 'callback')
                note.innerHTML = 'Thanks! We`ll contact you as soon as possible.<br><br>(click anywhere to close)';
            else
                note.innerHTML = text;
            note.classList.add('shown');
            document.querySelector('.wrapper').style.pointerEvents = 'none';
        }
    }

    $('.navNote').click(function (e) {
        e.preventDefault();

        $.ajax({
            type: 'POST',
            url: '../models/ajax.php',
            data: {'action': 'getNotes'},
            success: function (msg) {
                if (JSON.parse(msg).status === 'true') {
                    let str = '';
                    if (JSON.parse(msg).hasOwnProperty('start')) {
                        str += '<p><br>Current works:<br><br>';
                        let i = 0;
                        while (i < JSON.parse(msg).start.length) {
                            link = "../views/works.php?param=" + JSON.parse(msg).work_id[i];
                            str += '<a href="' + link + '"><br>Location: ' + JSON.parse(msg).location[i];
                            str += '<br>Start: ' + JSON.parse(msg).start[i];
                            str += '<br>End: ' + JSON.parse(msg).end[i] + '<br></a><br></p>';
                            i++;
                        }
                    }
                    if (JSON.parse(msg).hasOwnProperty('tag')) {
                        let i = 0;
                        while (i < JSON.parse(msg).tag.length) {
                            if (JSON.parse(msg).tag[i] === 'New user' && JSON.parse(msg).hasOwnProperty('subject'))
                                link = "../views/users.php?param=" + JSON.parse(msg).subject[i];
                            else if (JSON.parse(msg).tag[i] === 'Overtime' && JSON.parse(msg).hasOwnProperty('work_id'))
                                link = "../views/works.php?param=" + JSON.parse(msg).work_id[i];
                            str += '<a href=' + link + '><p><b>' + JSON.parse(msg).tag[i] + '</b><br>';
                            str += '<br>' + JSON.parse(msg).text[i];
                            str += '<br>Subject ID: ' + JSON.parse(msg).subject[i];
                            str += '<br>' + JSON.parse(msg).date[i] + '<br><br></p></a>';

                            i++;
                        }
                    }
                    ShowNotification(str);
                } else {
                    ShowNotification('No notifications for you');
                }
            }
        });
    });

    function HideNotification() {
        let note = document.querySelector('.notification');
        if (note) {
            note.innerHTML = '';
            note.classList.remove('shown');
            document.querySelector('.wrapper').style.pointerEvents = 'all';
            //document.body.style.overflow = 'auto';
        }
    }
    $('#submitCallback').click(function (e) {
        e.preventDefault();
        let name = document.querySelector('#nameCall').value;
        let phone = document.querySelector('#phoneCall').value;
        let email = document.querySelector('#emailCall').value;
        let data = {'nameCall': name};
        if (phone)
            data.phoneCall = phone;
        if (email)
            data.emailCall = email;
        $.ajax({
            type: 'POST',
            url: '../models/ajax.php',
            data: data,
            success: function (msg) {
                if (JSON.parse(msg).status === 'sent') {
                    ShowNotification('callback');
                } else if (JSON.parse(msg).status === 'error') {
                    ShowNotification('Error occurred, please, try again');
                }
            }
        });
    });

    $(document).click(function (e) {
        if(document.querySelector('.notification') !== null && document.querySelector('.notification').innerHTML.length > 0 && e.target.id !== 'submitCallback' && !e.target.classList.contains('navNote') && !e.target.closest('.notification')) {
            HideNotification();
        }
    });



    $('#submitReg').click(function (e) {
        e.preventDefault();
        let user = document.querySelector('#userReg').value;
        let pass = document.querySelector('#passReg').value;
        let passRepeat = document.querySelector('#passReg1').value;
        if ((pass.length < 6 || passRepeat.length < 6) && user !== 'admin') {
            ShowError('Password must be no shorter than 6 symbols');
        } else {
            HideError();

            $.ajax({
                type: 'POST',
                url: '../models/ajax.php',
                data: {'userReg': user, 'passReg': pass, 'passReg1': passRepeat},
                success: function (msg) {
                    if (JSON.parse(msg).status === 'registered') {
                        HideError();
                        window.location.href = '../views/main.php';
                    } else if (JSON.parse(msg).status === 'insert error') {
                        ShowError('Error occurred, please, check data and try again');
                    } else if (JSON.parse(msg).status === 'different passwords') {
                        ShowError('Passwords are not the same, please, check input and try again');
                    }
                }
            });
        }
    });
});