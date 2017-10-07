<div class="head">
    <div class="advs">
        <div class="body"><a href="#" class="logo"></a><i class="tenet"></i></div>
        <div class="menubar">
            <div class="menu"><?php echo $nav; ?></div>
        </div>
        <div class="advbar"></div>
    </div>
</div>
<?php
if(!empty($advertiser) && is_array($advertiser) && count($advertiser)>0)
{
    $advs = array();
    foreach($advertiser as $k=>$v){
        $advs[] = $v['pic'];
    }
    echo '<script>$(function(){';
   echo '$(".head").bgStretcher({images:[\''. Util::Impode($advs, "','") . '\'], imageWidth: 1920, imageHeight: 550, slideDirection: \'N\', slideShowSpeed: 2000, transitionEffect: \'fade\', sequenceMode: \'normal\', buttonPrev: \'#prev\', buttonNext: \'#next\', pagination: \'#nav\', anchoring: \'left center\', anchoringImg: \'left center\'});';
   echo '});</script>';
}
