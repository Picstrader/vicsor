<section id="modalCookieModal" style="display:none" class="modal-cookie">
    <div class="modal-cookie-container">
        <div class="modal-cookie-block">
            <div class="modal-cookie-text">
                <?= $fs['cookie text'] ?>
            </div>
            <div class="modal-cookie-button-cover">
                <button onclick="confirmCookie()" class="modal-cookie-button">
                    <?= strtoupper($fs['Confirm']) ?>
                </button>
            </div>
        </div>
    </div>
</section>