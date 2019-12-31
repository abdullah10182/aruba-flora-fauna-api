<?php


function getBaseUrl($auth=false){
    
    $httaccessAuth= '';
    $domain = '';
    $prefix = '';
    $httaccessAuthEnabled = true;
    $ssh_enabled = true;
    
    $domain = $_SERVER['HTTP_HOST'];
    //https enabled
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' && $ssh_enabled) { 
      $prefix = 'https://';
    }else
      $prefix = 'http://'; 
    
 
    // if($httaccessAuthEnabled && $domain == 'lab.webaruba.com'){
    //   $httaccessAuth = 'admin:webaruba123@';
    // }

    if($auth){
      $base_url = $prefix . $httaccessAuth . $domain;
     }else {
        $base_url = $prefix . $domain;
     }
     
    return $base_url;
}

function getToken($base_url, $session_name, $session_id){
    $ch = curl_init();
    curl_setopt_array($ch, array(
      CURLOPT_URL => $base_url . '/rest/session/token',
      CURLOPT_RETURNTRANSFER =>true,
      CURLOPT_HTTPHEADER => array(
            'Accept: application/hal+json',
            'Cookie: '.$session_name.'='.$session_id,
        ),
    ));
    $token=curl_exec($ch);    
    curl_close($ch);
    return $token;
}

function zeroFill ($num, $zerofill = 2){
	return str_pad($num, $zerofill, '0', STR_PAD_LEFT);
}

function checkForSelectedClientId($selectedClientId, $roles, $user_id){
  foreach ($roles as $key => $role) {
    if($role == 'dashboard_admin' && $selectedClientId){
      return $selectedClientId;
    }
  }
  return $user_id;
}

function emailCreateRegistrationClient($key, $to, $data, $mailManager){
    require 'email-templates/create-registration-email-template.php';;
    $params['message'] ='message';
    $subject = '';
    if($data['lang']=='en')
      $subject = 'WEB N.V. Plant Tour Registration Request Received';
    else
      $subject = 'WEB N.V. Peticion Ricibi pa Registracion Tour den Planta';
    $params = array(
      'subject' => $subject,
      'body' => $registration_created_template_client
    );
    //print_r($params);die;
    $langcode = \Drupal::currentUser()->getPreferredLangcode();
    $send = true;
    $mail_result = $mailManager->mail('aff_api', $key, $to, $langcode, $params, NULL, $send);

    if ($mail_result['result'] !== true) {
     $result = "error";
    }else {
     $result = "success";
    }

    return $result;
}

function emailCreateRegistrationAdmin($key, $to, $data, $mailManager){
    require 'email-templates/create-registration-email-template.php';
    $params['message'] ='message';
    $params = array(
      'subject' => 'Registration ' . $data["tourTypeSettingsAdmin"]["title"] . ' created',
      'body' => $registration_created_template_admin
    );
    $langcode = \Drupal::currentUser()->getPreferredLangcode();
    $send = true;
    $mail_result = $mailManager->mail('aff_api', $key, $to, $langcode, $params, NULL, $send);
}

function emailRegistrationApprovedClient($key, $to, $data, $mailManager, $type){
  $subject = '';
  if($data['nodeData']['field_language'][0]['value'] == 'en')
    $subject = 'Your tour registration has been approved';
  else
    $subject = 'Bo registracion pa tour ta aproba';

  require 'email-templates/edit-registration-email-template.php';
  $params['message'] ='message';
  $params = array(
    'subject' => $subject,
    'body' => $registration_edited_template_client
  );
  //print_r($params);die;
  $langcode = \Drupal::currentUser()->getPreferredLangcode();
  $send = true;
  $mail_result = $mailManager->mail('aff_api', $key, $to, $langcode, $params, NULL, $send);

  if ($mail_result['result'] !== true) {
   $result = "mail not sent";
  }else {
   $result = "Notification email sent to " . $to;
  }

  return $result;
}

function emailRegistrationRescheduledClient($key, $to, $data, $mailManager, $type){
  $subject = '';
  if($data['nodeData']['field_language'][0]['value'] == 'en')
    $subject = 'Your tour registration has been rescheduled';
  else
    $subject = 'Bo tour a wordo cambia di fecha';
  require 'email-templates/edit-registration-email-template.php';
  $params['message'] ='message';
  $params = array(
    'subject' => $subject,
    'body' => $registration_rescheduled_template_client
  );
  //print_r($params);die;
  $langcode = \Drupal::currentUser()->getPreferredLangcode();
  $send = true;
  $mail_result = $mailManager->mail('aff_api', $key, $to, $langcode, $params, NULL, $send);

  if ($mail_result['result'] !== true) {
   $result = "mail not sent";
  }else {
   $result = "Notification email sent to " . $to;
  }

  return $result;
}

function emailRegistrationCanceledClient($key, $to, $data, $mailManager, $type){
  $subject = '';
  if($data['registration']['lang'] == 'en')
    $subject = 'Your tour registration has been canceled';
  else
    $subject = 'Bo registracion di tour a wordo cancela';
  require 'email-templates/edit-registration-email-template.php';
  $params['message'] ='message';
  $params = array(
    'subject' => $subject,
    'body' => $registration_canceled_template_client
  );
  //print_r($params);die;
  $langcode = \Drupal::currentUser()->getPreferredLangcode();
  $send = true;
  $mail_result = $mailManager->mail('aff_api', $key, $to, $langcode, $params, NULL, $send);

  if ($mail_result['result'] !== true) {
   $result = "mail not sent";
  }else {
   $result = "Notification email sent to " . $to;
  }

  return $result;
}

function emailRegistrationCanceledByClient($key, $to, $data, $mailManager, $type){
  $subject = '';
  if($data['field_language'][0]['value'] == 'en')
    $subject = 'Your tour registration has been canceled by you';
  else
    $subject = 'Bo registracion di tour a wordo cancela door di bo persona';
  require 'email-templates/edit-registration-email-template.php';
  $params['message'] ='message';
  $params = array(
    'subject' => $subject,
    'body' => $registration_canceled_template_by_client
  );
  //print_r($params);die;
  $langcode = \Drupal::currentUser()->getPreferredLangcode();
  $send = true;
  $mail_result = $mailManager->mail('aff_api', $key, $to, $langcode, $params, NULL, $send);

  if ($mail_result['result'] !== true) {
   $result = "mail not sent";
  }else {
   $result = "Notification email sent to " . $to;
  }

  return $result;
}

function emailRegistrationCanceledByClientAdmin($key, $to, $data, $mailManager, $type){
  require 'email-templates/edit-registration-email-template.php';
  $params['message'] ='message';
  $params = array(
    'subject' => 'Registration cancelled by client',
    'body' => $registration_canceled_template_by_client_admin
  );
  $langcode = \Drupal::currentUser()->getPreferredLangcode();
  $send = true;
  $mail_result = $mailManager->mail('aff_api', $key, $to, $langcode, $params, NULL, $send);
}

function emailOrderApprovedAdmin($key, $to, $data, $mailManager){
  require 'email-templates/edit-order-email-template.php';
  $params['message'] ='message';
  $params = array(
    'subject' => $data['order_state_name'] . ' | ' . $data['title'],
    'body' => $order_approved_template_admin
  );
  //print_r($params);die;
  $langcode = \Drupal::currentUser()->getPreferredLangcode();
  $send = true;
  $mail_result = $mailManager->mail('aff_api', $key, $to, $langcode, $params, NULL, $send);

}

function getDashboardAdminSettings(){
  $settings = \Drupal::entityTypeManager()->getStorage('tours')->load('5');

  //emails
  $emails = $settings->field_admin_emails->getValue();
  $emailsArray = [];
  for ($i=0; $i < count($emails); $i++) { 
    $emailsArray[$i] = $emails[$i]['value'];
  }
  $emailsString = implode(', ', $emailsArray);

  $settingsToReturn = [];
  $settingsToReturn['emails'] = $emailsString;

  return $settingsToReturn;
}

function emailTourAdmins($key, $to, $data, $mailManager){
  $data['tableRegistrations'] = generateHtmlTable($data);
  require 'email-templates/tour-admin-email-template.php';
  $params['message'] ='message';
  $params = array(
    'subject' => 'Plant Tour Registration: ' . $data['tourDate'],
    'body' => $tour_admin_email_template
  );
  $langcode = \Drupal::currentUser()->getPreferredLangcode();
  $send = true;
  $mail_result = $mailManager->mail('aff_api', $key, $to, $langcode, $params, NULL, $send);

  if ($mail_result['result'] !== true) {
   $result = "mail not sent";
  }else {
   $result = "Notification email sent to " . $to;
  }

  return $result;
}

function generateHtmlTable($data){
  $registrations_table = '';

  foreach ($data['registrationsApproved'] as $key => $registration) {
      $registrations_table .= '<tr>';
      $registrations_table .= '<td>' . $registration['id'] . '</td>';
      $registrations_table .= '<td>' . $registration['typeTitle'] . '</td>';
      $registrations_table .= '<td>' . $registration['firstName'] . ' ' . $registration['lastName'] . '</td>';
      $registrations_table .= '<td>' . $registration['dob'] . '</td>';
      $registrations_table .= '<td>' . $registration['address'] . '</td>';
      $registrations_table .= '<td>' . $registration['phone'] . '</td>';
      if($registration['people_in_group'] && isset($registration['people_in_group']))
        $registrations_table .= '<td>' . $registration['people_in_group'] . '</td>';
      else
        $registrations_table .= '<td>NA</td>';
      $registrations_table .= '</tr>';
  }

  return $registrations_table;
}