$(document).ready(function () {
    const getCellValue = (tr, idx) => tr.children[idx].innerText || tr.children[idx].textContent;

    const comparer = (idx, asc) => (a, b) => ((v1, v2) =>
            v1 !== '' && v2 !== '' && !isNaN(v1) && !isNaN(v2) ? v1 - v2 : v1.toString().localeCompare(v2)
    )(getCellValue(asc ? a : b, idx), getCellValue(asc ? b : a, idx));

    document.querySelectorAll('th')?.forEach(th => th.addEventListener('click', (() => {
        const table = th.closest('table');
        Array.from(table.querySelectorAll('tr:nth-child(n+2)'))
            .sort(comparer(Array.from(th.parentNode.children).indexOf(th), this.asc = !this.asc))
            .forEach(tr => table.appendChild(tr));
    })));

    document.querySelectorAll('tr[data-id]')?.forEach(tr => tr.addEventListener('click', ((e) => {
        if (e.target.tagName !== 'INPUT' && !e.target.closest('.dropdown'))
            tr.classList.toggle('selected');
    })));

    document.querySelector('#out')?.addEventListener('click', (() => {
        window.location.href = '../views/login.php';
    }));

    document.querySelector('#navBackup')?.addEventListener('click', (e) => {
        e.preventDefault();
        $.ajax({
            url: "../models/backup.php",
            success: function(msg) {
                alert(msg);
            }
        });
    });

    document.querySelector('#navRestore')?.addEventListener('click', (e) => {
        e.preventDefault();
        $.ajax({
            url: "../models/restore.php?db=test",
            success: function(msg) {
                alert(msg);
            }
        });
    });

    document.querySelector('#delete')?.addEventListener('click', (() => {
        let selected = document.querySelectorAll('.selected');
        let ids = '';
        selected?.forEach(row => {
            ids += row.dataset.id + ',';
        });
        ids = ids.substr(0, ids.length-1);
        $.ajax({
            type: 'POST',
            url: '../models/ajax.php',
            data: {'action': 'delete', 'target': 'employee', 'ids': ids},
            success: function (msg) {
                if (JSON.parse(msg).status === 'deleted') {
                    alert('deleted successfully');
                    location.reload();
                } else if (JSON.parse(msg).status === 'deletion error') {
                    alert('error occurred, please, try again');
                }
            }
        });
    }));

    document.querySelector('#delete_works')?.addEventListener('click', (() => {
        let selected = document.querySelectorAll('.selected');
        let ids = '';
        let details = '';
        selected?.forEach(row => {
            ids += row.dataset.id + ',';
            details += row.dataset.detail + ',';
        });
        ids = ids.substr(0, ids.length-1);
        details = details.substr(0, details.length-1);
        $.ajax({
            type: 'POST',
            url: '../models/ajax.php',
            data: {'action': 'delete', 'target': 'work', 'ids': ids, 'details': details},
            success: function (msg) {
                if (JSON.parse(msg).status === 'deleted') {
                    alert('deleted successfully');
                    location.reload();
                } else if (JSON.parse(msg).status === 'deletion error') {
                    alert('error occurred, please, try again');
                }
            }
        });
    }));

    document.querySelector('#addit')?.addEventListener('click', (() => {
        document.querySelector('#saveAdd')?.classList.toggle('shown');
        document.querySelector('#additional')?.classList.toggle('shown');
    }));

    document.querySelector('#addit_works')?.addEventListener('click', (() => {
        document.querySelector('#saveAdd_works')?.classList.toggle('shown');
        document.querySelector('#additional_works')?.classList.toggle('shown');
    }));

    document.querySelector('#addTask')?.addEventListener('click', (() => {
        document.querySelector('#saveTask')?.classList.toggle('shown');
        document.querySelector('#additionalTask')?.classList.toggle('shown');
    }));

    document.querySelector('#saveAdd')?.addEventListener('click', (() => {
        let values = '';
        let inputs = document.querySelectorAll('#additional input');
        inputs?.forEach(input => {
            if (input.value.length < 1)
                values += '-,';
            else {
                values += input.value + ',';
            }

        });
        values = values.substr(0, values.length - 1);

        $.ajax({
            type: 'POST',
            url: '../models/ajax.php',
            data: {'action': 'add', 'target': 'employee', 'values': values},
            success: function (msg) {
                console.dir(JSON.parse(msg).status);
                if (JSON.parse(msg).status === 'added') {
                    alert('added successfully');
                } else
                    alert('error occurred, please, check data and try again');
            }
        });
    }));

    document.querySelector('#saveAdd_works')?.addEventListener('click', (() => {
        let values = '';
        let inputs = document.querySelectorAll('#additional_works input');
        inputs?.forEach(input => {
            if (input.value.length < 1)
                values += '-,';
            else {
                values += input.value + ',';
            }

        });
        values = values.substr(0, values.length - 1);

        $.ajax({
            type: 'POST',
            url: '../models/ajax.php',
            data: {'action': 'add', 'target': 'work', 'values': values},
            success: function (msg) {
                console.dir(JSON.parse(msg).status);
                if (JSON.parse(msg).status === 'added') {
                    alert('added successfully');
                } else
                    alert('error occurred, please, check data and try again');
            }
        });
    }));

    document.querySelector('#edit')?.addEventListener('click', (() => {
        if (document.querySelector('#save'))
            document.querySelector('#save').style.display = 'block';
        let selected = document.querySelectorAll('.selected');
        selected?.forEach(row => {
            row.querySelectorAll('td')?.forEach(el => {
               el.outerHTML = '<td><input value="' + el.innerHTML + '"></input></td>';
            });
        });
    }));

    document.querySelector('#edit_works')?.addEventListener('click', (() => {
        if (document.querySelector('#save_works'))
            document.querySelector('#save_works').style.display = 'block';
        let selected = document.querySelectorAll('.selected');
        selected?.forEach(row => {
            row.querySelectorAll('td')?.forEach(el => {
                if (el.childElementCount === 0) {
                    let outer = '<td data-id="' + el.dataset.id + '">';
                    if (el.dataset.id !== 'Start' && el.dataset.id !== 'End') {
                        outer += '<div class="options" style="position: absolute">';
                    }
                    outer += '</div><input value="' + el.innerHTML + '" data-value="' + el.innerHTML + '"></input></td>';
                    el.outerHTML = outer;
                }
            });
        });
    }));

    document.addEventListener('click', ((e) => {
        if (e.target.closest('tr td input') && e.target.closest('tr td .options')) {
            data = {'dropdown': e.target.parentElement.dataset.id};
            if (e.target.parentElement.dataset.id === 'Location')
                data.company = e.target.parentElement.previousElementSibling.querySelector('input').value;
            if (data.dropdown.length > 0 && ((data.hasOwnProperty('company') && data.company.length > 0) || !data.hasOwnProperty('company')))
                ShowSelect(data, e.target);
        }
    }));

    function ShowSelect(data, parent) {
        //parent === e.target
        if (parent.closest('tr td input').value.length > 0) {
            $.ajax({
                type: 'GET',
                url: '../models/works.php',
                data: data,
                success: function (msg) {
                    let select = parent.parentElement.firstElementChild;
                    if (select)
                        select.innerHTML = JSON.parse(msg);
                }
            });
        }
    }

    document.addEventListener('click', ((e) => {
        if (e.target.closest('select')) {
            let el = e.target.closest('.dropdown');
            let input = el.parentElement.nextElementSibling;
            input.value = el.options[el.selectedIndex].text;
            input.dataset.value_id = el.options[el.selectedIndex].value;
            if (input.value === 'New') {
                el.style.display = 'none';
                input.disabled = false;
                //input.value = '';
                input.placeholder = 'Insert new one:';
            }
            else {
                input.disabled = true;
            }
            if (el.closest('td').dataset.id === 'Company') {
                let data = {'dropdown': el.parentElement.parentElement.nextElementSibling.dataset.id};
                data.company = el.parentElement.parentElement.nextElementSibling.previousElementSibling.querySelector('input').value;
                if (data.dropdown.length > 0 && data.company.length > 0)
                    ShowSelect(data, el.parentElement.parentElement.nextElementSibling.querySelector('input'))
            }
        }
    }));

    document.addEventListener('click', ((e) => {
        if(!e.target.closest('.table') && document.querySelector('td input') && e.target.id !== 'edit' && !e.target.closest('#additional')) {
            if(document.querySelector('#save'))
                document.querySelector('#save').style.display = 'none';
            document.querySelectorAll('.table td input')?.forEach(el => {
                el.outerHTML = '<td>' + el.value + '</td>';
            });
        }
    }));

    document.addEventListener('click', ((e) => {
        if(!e.target.closest('.table_workers') && document.querySelector('td input') && e.target.id !== 'edit_works' && !e.target.closest('#additional_works')) {
            if(document.querySelector('#save_works'))
                document.querySelector('#save_works').style.display = 'none';
            document.querySelectorAll('.table_workers td input')?.forEach(el => {
                el.outerHTML = '<td>' + el.value + '</td>';
            });
        }
    }));

    document.querySelector('#save')?.addEventListener('click', (() => {
        let values = '';

        $.each(document.querySelectorAll('.selected'), function (i, el) {
            if (el.querySelectorAll('td input')?.length > 0) {
                values = '';
                el.querySelectorAll('td input')?.forEach(e => {
                    if (e.value.length < 1)
                        values += '-,';
                    else
                        values += e.value + ',';
                });
                values = values.substr(0, values.length - 1);
                let ids = el.dataset.id;
                $.ajax({
                    type: 'POST',
                    url: '../models/ajax.php',
                    data: {'action': 'update', 'target': 'employee', 'ids': ids, 'values': values},
                    success: function (msg) {
                        if (JSON.parse(msg).status === 'updated') {
                            alert('updated successfully');
                        } else
                            alert('error occurred, please, check data and try again');
                    }
                });
            }
        });
    }));

    document.querySelector('#save_works')?.addEventListener('click', (() => {
        let values = '';

        $.each(document.querySelectorAll('.selected'), function (i, el) {
            if (el.querySelectorAll('td input')?.length > 0) {
                values = '';
                let columns = '';
                let newvalues = '';
                let newcolumns = '';
                el.querySelectorAll('td input')?.forEach(e => {
                    if (e.parentElement.dataset.id === 'Start' || e.parentElement.dataset.id === 'End') {
                        if (e.value.length < 1)
                            values += '-,';
                        else {
                            values += e.value + ',';
                            columns += e.parentElement.dataset.id + 'Time,';
                        }
                    } else {
                        if (e.dataset.hasOwnProperty('value_id') && e.dataset.value_id.length > 0) {
                            if (e.parentElement.dataset.id !== 'Company') {
                                if (e.dataset.value_id !== 'new') {
                                    if (e.parentElement.dataset.id === 'Responsible')
                                        values += e.value + ',';
                                    else
                                        values += e.dataset.value_id + ',';
                                    columns += e.parentElement.dataset.id + ',';
                                } else {
                                    newvalues += e.value + ',';
                                    newcolumns += e.parentElement.dataset.id + ',';
                                }
                            }
                        }
                    }
                });
                values = values.substr(0, values.length - 1);
                columns = columns.substr(0, columns.length - 1);
                newvalues = newvalues.substr(0, newvalues.length - 1);
                newcolumns = newcolumns.substr(0, newcolumns.length - 1);
                let ids = el.dataset.id;
                let ids_detail = el.dataset.detail;
                $.ajax({
                    type: 'POST',
                    url: '../models/ajax.php',
                    data: {'action': 'update', 'target': 'work', 'ids': ids, 'ids_detail': ids_detail, 'values': values, 'columns': columns, 'newvalues': newvalues, 'newcolumns': newcolumns},
                    success: function (msg) {
                        if (JSON.parse(msg).status === 'updated') {
                            alert('updated successfully');
                        } else
                            alert('error occurred, please, check data and try again');
                    }
                });
            }
        });
    }));
});