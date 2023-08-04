<?php
if (isLogin()) {
	$images = getUserImages(getLoginUserId());
} else {
	$images = [];
}
$all_sets = getAllSets();
?>
<section id="trade_gallery" class="trade_gallery">
	<?php for ($i = 0; $i < 7; $i++) { ?>
		<?php if (count($images) >= $i + 1) {
			if ($images[$i]['status'] === 'trading') {
				foreach ($all_sets as $existing_set) {
					$players = $existing_set['users_photos'] === '' ? [] : explode(';', $existing_set['users_photos']);
					foreach ($players as $player) {
						$player_data = explode(':', $player);
						if ($player === '' || !isset($player_data[1]))
							break;
						if ((int) $player_data[1] == (int) $images[$i]['id']) {
							$images[$i]['set_id'] = $existing_set['id'];
							break 2;
						}
					}
				}
			}
			?>
			<div class="trade_gallery__section">
				<div class="trade_gallery__section_foto-mobile trade_gallery__section_foto selected<?= $images[$i]['id'] ?>"
					style="background-image: url(./inc/assets/img/<?= $images[$i]['name_thumbnail'] ?>);"
					data-image='<?= json_encode($images[$i]) ?>'>
					<div class="trade_gallery__section_foto_hover">
					<?php if ($images[$i]['status'] != 'trading') { ?>
						<div class="delete__image-trade">
								<a class="trade_gallery__delete"
									onclick="deleteImage(<?= $images[$i]['id'] ?>, '<?= $images[$i]['status'] ?>')"><?= $fs['Delete'] ?><img
										class="img__del" src='/inc/assets/img/delimage.svg'></a>
						</div>
						<?php } ?>
						<?php if ($images[$i]['status'] === 'trading') { ?>
						<div class="hash__image-trade">
						</div>
						<?php } ?>

					</div>
				</div>
				<div class="trade_gallery__section-img_description_block">
					<div class="trade_gallery__section-img_description">
						<?php switch ($images[$i]['status']) {
							case 'ready':
								echo($fs['ready']);
								break;
							case 'trading':
								?>
								<span><?= $fs['Foto in set'] ?></span>
								<?php
								break;
							case 'moderating':
								echo($fs['moderating']);
								break;
							default:
								echo($fs['ready']);
								break;
						} ?>
					</div>
				</div>
			</div>
		<?php } else { ?>
			<div class="trade_gallery__empty_section_cover" onclick="<?= isLogin() ? 'showUploadImage(this)' : 'notLogin()' ?>">
				<div class="trade_gallery__empty_section">
					<div class="trade_gallery__empty_section-ellipse">
						<img class="trade_gallery__empty_section-ellipse-add" src="./inc/assets/img/trade_slider-add.png">
					</div>
				</div>
                <div class="trade_gallery__no-image">
                    <span><?= $fs['Upload a photo'] ?></span>
                </div>
			</div>
		<?php } ?>
	<?php } ?>
	<?php if (isLogin()) { ?>
		<input type="file" id="trade_slider__upload_file" name="trade_slider__upload_file" style="display:none"
			onchange="uploadImage()" />
	<?php } ?>
</section>