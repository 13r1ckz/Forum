<?
include "default.php";
//ayy lmao
if($username == "13r1ckz") {
	echo "<audio autoplay><source src='https://my.mixtape.moe/vferzj.mp3' type='audio/mp3'></audio>";
}
?>
<head>
  <title>Test</title>
  <style>
    input.checkbox {
      display: none;
    }
    .checkbox .icon {
      margin-right: 5px;
    }
    .icon.visible {
      position: absolute;
      transition: opacity .4s ease, -webkit-transform .3s ease;
      transition: opacity .4s ease, transform .3s ease;
      transition: opacity .4s ease, transform .3s ease, -webkit-transform .3s ease;
    }    
    .icon.hidden {
      position: absolute;
      transition: opacity .6s ease, -webkit-transform .6s ease;
      transition: opacity .6s ease, transform .6s ease;
      transition: opacity .6s ease, transform .6s ease, -webkit-transform .6s ease;
    }
    .icon.visible:before {
      content: "\f10c";
    }
    .icon.hidden:before {
      content: "\f111";
    }
    .icon.visible {
      opacity: 1;
      -webkit-transform: scale(1);
      transform: scale(1);
    }
    .icon.hidden {
       opacity: 0;
      -webkit-transform: scale(0.1);
      transform: scale(0.1);
    } 
    .checkbox:checked + label .icon.visible {
      opacity: 0;
      -webkit-transform: scale(1.75);
      transform: scale(1.75);
    }
    .checkbox:checked + label .icon.hidden {
      opacity: 1;
      -webkit-transform: scale(1);
      transform: scale(1);
    }
  </style>
</head>
<div class="container content">
  <? if(isset($message)) { echo $message; } ?>
  <div class="breadcrumb">
    <a class="section" href="index.php"><? echo $brand ?></a>
    <div class="divider">
      /
    </div>
    <div class="active section">
      Test
    </div>
  </div>
  
  <div class="segment">
    <h1 class="header brand">
      Test
    </h1>
    <input type="radio" class="checkbox" id="radio1" name="test">
    <label for="radio1" class="checkbox"><div><i class="icon fa visible"></i><i class="icon fa hidden"></i></div>Test</label>
    <br>
    <input type="radio" class="checkbox" id="radio2" name="test">
    <label for="radio2" class="checkbox"><div style="padding-right: 5px;"><i class="icon fa visible"></i><i class="icon fa hidden"></i></div>Test</label>
  </div>
  
<?
if($_COOKIE['style'] == "v1") { include "footerv1.php"; }
?>
</div>
<?
if($_COOKIE['style'] == "v2" || !isset($_COOKIE['style'])) { include "footerv2.php"; }
?>