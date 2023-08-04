<div class="trade_submit">
	<div class="trade_submit__close">X</div>
	<div class="trade_submit__info">
		<div class="trade_submit__info-description">
			<p class="trade_submit__info-description-heading">Подтвердите ваш выбор</p>
			<p class="trade_submit__info-description-title">Информация о коллекции:</p>
			<div class="trade_submit__info-description-statistic">
				<p class="trade_submit__info-description-statistic-description500">Стоимость размещения:</p>
				<p class="trade_submit__info-description-statistic-value400">20,00 <?= $fs['main_currency'] ?></p>
				<p class="trade_submit__info-description-statistic-description500">Количество в коллекции:</p>
				<p class="trade_submit__info-description-statistic-value400">20</p>
				<p class="trade_submit__info-description-statistic-description400">Win:</p>
				<p class="trade_submit__info-description-statistic-value400">20</p>
				<p class="trade_submit__info-description-statistic-description500">Тема:</p>
				<p class="trade_submit__info-description-statistic-value500">Закат</p>
			</div>
			<div class="trade_submit__info-description-income">
				<p class="trade_submit__info-description-income-description500">Доходность:</p>
				<p class="trade_submit__info-description-income-value500">200%</p>
				<p class="trade_submit__info-description-income-description500">Вероятная прибыль:</p>
				<p class="trade_submit__info-description-income-value500">+1200,00 <?= $fs['main_currency'] ?></p>
			</div>
		</div>
		<img src="#" class="trade_submit__info-img">
	</div>
	<form class="trade_submit__form">
		<div class="trade_submit__form-agry">
			<input type="checkbox" name="checkbox" class="trade_submit__form-agry-checkbox">
			<p class="trade_submit__form-agry-info">
				Я принимаю
				<a href="#" class="trade_submit__form-agry-info-ancor">Условия и положения</a>,
				<a href="#" class="trade_submit__form-agry-info-ancor">Политика конфиденциальности</a>,
				<a href="#" class="trade_submit__form-agry-info-ancor">Политика AML</a>
			</p>
		</div>
		<div class="trade_submit__form-buttons">
			<input type="submit" name="cancel_btn" value="Отменить" class="trade_submit__form-buttons-cancel">
			<input type="submit" name="submit_btn" value="подтвердить" class="trade_submit__form-buttons-submit">
		</div>
	</form>
</div>