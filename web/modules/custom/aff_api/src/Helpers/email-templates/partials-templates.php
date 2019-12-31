<?php

$app_name = "Online Laboratory Platform";
$siteUrl = getBaseUrl();

$link_registration_admin = null;

if(isset($data['tourDateLinkAdmin'])){
    $link_registration_admin = '   
        <a 
        style="background:#0084B6;color:#fff;padding:10px 12px;
        text-align:center;text-decoration:none;width:220px;display:block;margin:0 auto;font-size:14px;"
        href="'.$siteUrl.'/tours-administration/registrations/'. $data['tourDateLinkAdmin'] .'"><strong>View Registration</strong></a>
    ';
}

if(isset($data['hash']) && isset($data['id'])){
    $text = 'Wish to cancel your tour registration? Click here';
    $url = '/company/plant-tours?id=';
    if(isset($data['nodeData']['field_language'][0]['value']) && $data['nodeData']['field_language'][0]['value'] == 'pap'){
        $text = 'Bo ta desea di cancela bo registracion di tour? Click aki';
        $url = '/pap/compania/tours?id=';
    }
    $link_cancel_registration = '   
        <p>
            <a style="color:#d9534f;font-size:12px;text-decoration:none;"
            href="'.$siteUrl.$url. $data['id'] .'&hash='. $data['hash'] .'"><strong>'.$text.'</strong></a>
        </p>
    ';
}

$link_registration_page = '   
    <a 
    style="background:#0084B6;color:#fff;padding:10px 12px;
    text-align:center;text-decoration:none;width:220px;display:block;margin:0 auto;font-size:14px;"
    href="'.$siteUrl.'/company/plant-tours"><strong>Download Waivers</strong></a>
';
 
$email_footer = '
    <br>
    <p>
        <a href="http://www.webaruba.com">
            <img width="600" src="http://www.webaruba.com/modules/custom/aff_api/vue-app/src/assets/img/logos/email-footer.png" alt="Home">
        </a>
    </p>          
';