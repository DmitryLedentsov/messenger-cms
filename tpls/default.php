<html lang="en">
<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
<title>
Скидка на второй заказ
</title>
<style type="text/css">

.content h2 {color:#b64769; font-weight: normal; margin: 40px 0 40px 0; padding: 0; line-height: 30px; font-size: 36px;font-family: 'helvetica', 'arial', sans-serif}
.content p {font-size: 16px; font-weight: 300; line-height: 28px;font-family: 'helvetica', 'arial', sans-serif}
.content a {color: #888}
.white-list {background: #fff !important; border: 1px solid #dadada;}
.white-list td {padding: 0px}
.codes {margin-bottom: 100px}
.code {background-color: #b64769; padding: 35px 15px; color: #fff; font-weight: normal; font-size: 20px; text-align: center; margin: 10px 0}
.logo {text-align: center; max-height: 100px !important; height: 100px}
.logo img {display: block; max-width: 200px; max-height: 100px; margin: 0 auto}
.clear {clear: both}
.background {background-color: #b64769}
@media (max-width: 767px) {
.footer p {font-size: 12px}
.logo {width: 50%}
}
</style>
</head>
<?php
	$options = P4Second::getOptions();
?>
<body style="margin: 0; padding: 20px; background: transparent">
	<table class="white-list" cellpadding="0" cellspacing="0" align="center" width="100%" style="font-family: 'helvetica', 'arial', sans-serif; margin: 0px auto; background: #fff; max-width: 600px">
		<tbody>
			<tr class="background" style="background-color: <?php echo $options['background_color'];?>">
				<td style="background-image: url(<?php echo ($options['background_image']) ? SITE.$options['background_image'] : SITE.'/mg-plugins/p4-second/tpls/default.jpg';?>); background-position: 50% 50%; background-repeat: no-repeat; background-size: cover; height: 250px"></td>
			</tr>
			<tr class="content">
				<td align="center" style="padding: 40px">
					<table style="max-width: 600px; width: 100%">
						<tr>
							<td style="text-align: center">
							
									<h2 style="<?php echo $options['h-color'];?>">Скидка на второй заказ</h2>
									<p style="font-size: 18px"><?php echo $count;?>  <?php echo $basePromoInfo['value']; ?> %</p>
					
								<div class="codes">
														
									<div class="code" style="<?php echo $options['c_color'];?>; background-color: <?php echo $options['background_color'];?>">
										<?php echo $basePromoInfo['code']; ?>
									</div>
								</div>
								<p style="color: #888; font-size: 13px; text-align: center;">Каждый сертификат дает фиксированную скидку и может быть использован единоразово при оформлении заказа на сайте <a href="<?php echo SITE;?>"><?php echo SITE;?></a>. Если у вас возникнут вопросы, напишите нам <a href="mailto:<?php echo MG::getSetting('noReplyEmail'); ?>"><?php echo MG::getSetting('adminEmail'); ?></a> или позвоните <a href="tel:<?php echo MG::getSetting('shopPhone'); ?>"><?php echo MG::getSetting('shopPhone'); ?></a></p>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
</body>
</html>