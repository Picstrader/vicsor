<section class="themes_by_abc">
	<div class="themes_by_abc__selection">
		<p class="themes_by_abc__selection-title">Темы</p>
		<form class="themes_by_abc__selection-form">
			<select class="themes_by_abc__selection-form-select" name="theme">
				<option class="themes_by_abc__selection-form-select-item" value="-1">Поиск темы</option>
				<option class="themes_by_abc__selection-form-select-item" value="0">Без темы</option>
				<option class="themes_by_abc__selection-form-select-item" value="1">Food</option>
				<option class="themes_by_abc__selection-form-select-item" value="2">Music</option>
			</select>
		</form>
	</div>
	<div class="themes_by_abc__abc_short">
		<a href="#" class="themes_by_abc__abc_short-all_themes_ancor">Все темы</a>
		<a href="#" class="themes_by_abc__abc_short-ancor">0-9</a>
		<?php 
			for ($i=ord('A'); $i<=ord('Z'); $i++){
				echo('<a href="&letter=' . chr($i) . '" class="themes_by_abc__abc_short-ancor">' . chr($i) . '</a>');
			}
		?>
	</div>
	<div class="themes_by_abc__abc_extended">
		<div class="themes_by_abc__abc_extended-group">
			<p class="themes_by_abc__abc_extended-group-title">0-9</p>
			<ul class="themes_by_abc__abc_extended-group-list">
				<?php
				for ($i=0; $i < 10; $i++) { 
					echo('<li class="themes_by_abc__abc_extended-group-list-item">
						<a href="#" class="themes_by_abc__abc_extended-group-list-item-ancor">Badgley' . 'Mishka Swimwear' . '</a>');
					if (true) {
						echo('<span class="themes_by_abc__abc_extended-group-list-item-new">New</span>');
					}
					echo '</li>';
				}
				?>
			</ul>
		</div>





		<?php
		for ($ii=ord('A'); $ii <= ord('Z'); $ii++) { 
			echo(
				'<div class="themes_by_abc__abc_extended-group">
					<p class="themes_by_abc__abc_extended-group-title">' . chr($ii) . '</p>
					<ul class="themes_by_abc__abc_extended-group-list">');
						for ($i=0; $i < 10; $i++) { 
							echo(
						'<li class="themes_by_abc__abc_extended-group-list-item">
							<a href="' . '#' . '" class="themes_by_abc__abc_extended-group-list-item-ancor">' . 'Badgley Mishka Swimwear' . '</a>'
							);
							if (true) {
								echo(
							'<span class="themes_by_abc__abc_extended-group-list-item-new">New</span>'
								);
							}
						echo(
						'</li>'
						);
						}
			echo(
					'</ul>
				</div>'
			);
		}
		?>





	</div>
</section>