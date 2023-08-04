<section class="trade_settings">
	<form class="trade_settings__form">
		<div class="trade_settings__form-find">
			<p class="trade_settings__form-find-theme">Тема</p>
			<input type="text" name="" placeholder="Поиск..." class="trade_settings__form-find-input">
		</div>
		<div class="trade_settings__form-tune">
			<p class="trade_settings__form-tune-title">Стоимость размещения 100</p>
			<div class="trade_settings__form-tune-value">
				<div class="trade_settings__form-tune-value-minus">-</div>
				<input type="range" name="" class="trade_settings__form-tune-value-range">
				<div class="trade_settings__form-tune-value-plus">+</div>
			</div>
		</div>
		<div class="trade_settings__form-tune">
			<p class="trade_settings__form-tune-title">Количество в коллекции 100</p>
			<div class="trade_settings__form-tune-value">
				<div class="trade_settings__form-tune-value-minus">-</div>
				<input type="range" name="" class="trade_settings__form-tune-value-range">
				<div class="trade_settings__form-tune-value-plus">+</div>
			</div>
		</div>
		<div class="trade_settings__form-tune">
			<p class="trade_settings__form-tune-title">Win 100</p>
			<div class="trade_settings__form-tune-value">
				<div class="trade_settings__form-tune-value-minus">-</div>
				<input type="range" name="" class="trade_settings__form-tune-value-range">
				<div class="trade_settings__form-tune-value-plus">+</div>
			</div>
		</div>
	</form>
	<div class="trade_settings__bottom">
		<p class="trade_settings__bottom-title">Доходность:</p>
		<p class="trade_settings__bottom-value">200%</p>
		<p class="trade_settings__bottom-title">В баланс:</p>
		<p class="trade_settings__bottom-value">+1200,00 <?= $fs['main_currency'] ?></p>
	</div>
</section>