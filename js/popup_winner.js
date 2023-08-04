checkUserWon();
let win_lose_popup = setInterval(checkUserWon, 10000);
function checkUserWon() {
    let form_data = new FormData();
    form_data.append('action', 'check_won');
    let xhr = new XMLHttpRequest();
    xhr.open("POST", location.origin + "/trade-ajax.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            let json = xhr.responseText;
            if (json != '') {
                let div = document.createElement('div');
                div.innerHTML = json;
                document.body.appendChild(div.firstChild);
                let modal_win = document.getElementById("myModalWinnerPop");
                modal_win.style.display = "block";
                updateBalanceInHeader();
                clearInterval(win_lose_popup);
            }
        }
    };
    xhr.send(form_data);
}

function hideModalWinnerPopap() {
    let modal_win = document.getElementById("myModalWinnerPop");
    modal_win.style.display = "none";
    reloadCurrentPage();
}