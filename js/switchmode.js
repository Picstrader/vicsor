var switchMode = document.getElementById("switchMode");
var menuBtn = document.querySelector('.menu-btn');
var menu = document.querySelector('.menu');

menuBtn.addEventListener('click', function () {
    menuBtn.classList.toggle('active');
    menu.classList.toggle('active');
})

switchMode.onclick = function () {
    let themeHeader = document.getElementById("header");
    let stylesHeader = document.getElementById("styles");
    let footerHeader = document.getElementById("footer");
    let tradeHeader = document.getElementById("trade");
    let tradeH1Header = document.getElementById("tradeH1");
    let tradeSliderH1Header = document.getElementById("trade-slider");
    let tradeModalH1Header = document.getElementById("trade-modal");
    let howItWorksFaqs = document.getElementById("how-it-works-faqs");
    let personalAccount = document.getElementById("personal-account");
    let login = document.getElementById("login");
    let registration = document.getElementById("registration");
    let gallery = document.getElementById("gallery");
    let terms = document.getElementById("terms");

    if (themeHeader.getAttribute("href") == "inc/assets/css/header.css") {
        document.cookie = "theme=dark";
        themeHeader.href = "inc/assets/css/header-dark.css";
        stylesHeader.href = "inc/assets/css/styles-dark.css";
        footerHeader.href = "inc/assets/css/footer-dark.css";
        tradeHeader.href = "inc/assets/css/trade-dark.css";
        tradeH1Header.href = "inc/assets/css/trade-h1-dark.css";
        tradeSliderH1Header.href = "inc/assets/css/trade-slider-dark.css";
        tradeModalH1Header.href = "inc/assets/css/trade-modal-dark.css";
        howItWorksFaqs.href = "inc/assets/css/how-it-works-faqs-dark.css";
        personalAccount.href = "inc/assets/css/personal-account-dark.css";
        login.href = "inc/assets/css/login-dark.css";
        registration.href = "inc/assets/css/registration-dark.css";
        gallery.href = "inc/assets/css/gallery-dark.css";
        terms.href = "inc/assets/css/terms-dark.css";
    } else {
        document.cookie = "theme=light";
        themeHeader.href = "inc/assets/css/header.css";
        stylesHeader.href = "inc/assets/css/styles.css";
        footerHeader.href = "inc/assets/css/footer.css";
        tradeHeader.href = "inc/assets/css/trade.css";
        tradeH1Header.href = "inc/assets/css/trade-h1.css";
        tradeSliderH1Header.href = "inc/assets/css/trade-slider.css";
        tradeModalH1Header.href = "inc/assets/css/trade-modal.css";
        howItWorksFaqs.href = "inc/assets/css/how-it-works-faqs.css";
        personalAccount.href = "inc/assets/css/personal-account.css";
        login.href = "inc/assets/css/login.css";
        registration.href = "inc/assets/css/registration.css";
        gallery.href = "inc/assets/css/gallery.css";
        terms.href = "inc/assets/css/terms.css";
    }
    switchConfirmModalButtonSrc();
}
