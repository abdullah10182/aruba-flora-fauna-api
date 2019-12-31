<?php

include 'partials-templates.php';

$registration_edited_template_client = null;
$registration_rescheduled_template_client = null;
$registration_canceled_template_client = null;


if($type=='approve' && $data['nodeData']['field_language'][0]['value'] == 'en'){
    $registration_edited_template_client = '
        <html>
            <body style="font-family: arial;width:600px;color:#757575;">
                <h2 style="color:#0084B6"> Hi '.$data["nodeData"]["field_first_name"][0]["value"].' '.$data["nodeData"]["field_last_name"][0]["value"].',</h2>
                <p style="font-size:16px;">
                    Your registration request has been approved.
                </p>
    
                <h4><strong>Registration Info:</strong></h4>
                <table rules="all" style="border-color: #666;" cellpadding="10">
                <tr>
                    <td><strong>Tour Type</strong></td>
                    <td>'. $data["tourTypeSettings"]["title"][0]["value"] .'</td>
                </tr>
                <tr>
                    <td><strong>Tour Date/ Time</strong></td>
                    <td>'. $data["nodeData"]["field_tour_date"][0]["value"] .'</td>
                </tr>
                <tr>
                    <td><strong>First Name</strong></td>
                    <td>'. $data["nodeData"]["field_first_name"][0]["value"] .'</td>
                </tr>
                <tr>
                    <td><strong>Last Name</strong></td>
                    <td>'. $data["nodeData"]["field_last_name"][0]["value"] .'</td>
                </tr>
                <tr>
                    <td><strong>Date of Birth</strong></td>
                    <td>'. $data["nodeData"]["field_date_of_birth"][0]["value"] .'</td>
                </tr>
                <tr>
                    <td><strong>Address</strong></td>
                    <td>'. $data["nodeData"]["field_address"][0]["value"] .'</td>
                </tr>
                <tr>
                    <td><strong>Phone</strong></td>
                    <td>'. $data["nodeData"]["field_phone"][0]["value"] .'</td>
                </tr>
                <tr>
                    <td><strong>E-mail</strong></td>
                    <td>'. $data["nodeData"]["field_email"][0]["value"] .'</td>
                </tr>
            </table>
            <br>
                <p style="font-size:18px;">
                    ' . $data["tourTypeSettings"]["field_email_approved_text"][0]["value"] . '
                </p>
                '. $link_cancel_registration .'
                '. $email_footer .'
            </body>
        </html>
    ';
}

if($type=='approve' && $data['nodeData']['field_language'][0]['value'] == 'pap'){
    $registration_edited_template_client = '
        <html>
            <body style="font-family: arial;width:600px;color:#757575;">
                <h2 style="color:#0084B6"> Bon dia '.$data["nodeData"]["field_first_name"][0]["value"].' '.$data["nodeData"]["field_last_name"][0]["value"].',</h2>
                <p style="font-size:16px;">
                    Bo peticion di registracion ta aproba. 
                </p>
    
                <h4><strong>Informacion di registracion:</strong></h4>
                <table rules="all" style="border-color: #666;" cellpadding="10">
                <tr>
                    <td><strong>Tipo di tour</strong></td>
                    <td>'. $data["tourTypeSettings"]["title"][0]["value"] .'</td>
                </tr>
                <tr>
                    <td><strong>Fecha y Ora di Tour</strong></td>
                    <td>'. $data["nodeData"]["field_tour_date"][0]["value"] .'</td>
                </tr>
                <tr>
                    <td><strong>Nomber</strong></td>
                    <td>'. $data["nodeData"]["field_first_name"][0]["value"] .'</td>
                </tr>
                <tr>
                    <td><strong>Fam</strong></td>
                    <td>'. $data["nodeData"]["field_last_name"][0]["value"] .'</td>
                </tr>
                <tr>
                    <td><strong>Fecha di Nacemento</strong></td>
                    <td>'. $data["nodeData"]["field_date_of_birth"][0]["value"] .'</td>
                </tr>
                <tr>
                    <td><strong>Adres</strong></td>
                    <td>'. $data["nodeData"]["field_address"][0]["value"] .'</td>
                </tr>
                <tr>
                    <td><strong>Telefon</strong></td>
                    <td>'. $data["nodeData"]["field_phone"][0]["value"] .'</td>
                </tr>
                <tr>
                    <td><strong>E-mail</strong></td>
                    <td>'. $data["nodeData"]["field_email"][0]["value"] .'</td>
                </tr>
            </table>
            <br>
                <p style="font-size:18px;">
                    ' . $data["tourTypeSettings"]["field_email_approved_text"][0]["value"] . '
                </p>
                '. $link_cancel_registration .'
                '. $email_footer .'
            </body>
        </html>
    ';
}

if($type=='reschedule' && $data['nodeData']['field_language'][0]['value'] == 'en'){
    $registration_rescheduled_template_client = '
        <html>
            <body style="font-family: arial;width:600px;color:#757575;">
                <h2 style="color:#0084B6"> Hi '.$data["nodeData"]["field_first_name"][0]["value"].' '.$data["nodeData"]["field_last_name"][0]["value"].',</h2>
                <p style="font-size:16px;">
                    Your registration has been rescheduled from <strong> '. $data['oldTourDate'] .'</strong> 
                    to <strong> '. $data['newTourDate'] . ' </strong>
                </p>

                <h4><strong>Registration Info:</strong></h4>
                <table rules="all" style="border-color: #666;" cellpadding="10">
                <tr>
                    <td><strong>Tour Type</strong></td>
                    <td>'. $data["tourTypeSettings"]["title"][0]["value"] .'</td>
                </tr>
                <tr>
                    <td><strong>Tour Date/ Time</strong></td>
                    <td>'. $data["nodeData"]["field_tour_date"][0]["value"] .'</td>
                </tr>
                <tr>
                    <td><strong>First Name</strong></td>
                    <td>'. $data["nodeData"]["field_first_name"][0]["value"] .'</td>
                </tr>
                <tr>
                    <td><strong>Last Name</strong></td>
                    <td>'. $data["nodeData"]["field_last_name"][0]["value"] .'</td>
                </tr>
                <tr>
                    <td><strong>Date of Birth</strong></td>
                    <td>'. $data["nodeData"]["field_date_of_birth"][0]["value"] .'</td>
                </tr>
                <tr>
                    <td><strong>Address</strong></td>
                    <td>'. $data["nodeData"]["field_address"][0]["value"] .'</td>
                </tr>
                <tr>
                    <td><strong>Phone</strong></td>
                    <td>'. $data["nodeData"]["field_phone"][0]["value"] .'</td>
                </tr>
                <tr>
                    <td><strong>Email</strong></td>
                    <td>'. $data["nodeData"]["field_email"][0]["value"] .'</td>
                </tr>
            </table>
            <br>
                <p style="font-size:18px;">
                    ' . $data["tourTypeSettings"]["field_email_approved_text"][0]["value"] . '
                </p>
                '. $link_cancel_registration .'
                '. $email_footer .'
            </body>
        </html>
    ';
}

if($type=='reschedule' && $data['nodeData']['field_language'][0]['value'] == 'pap'){
    $registration_rescheduled_template_client = '
        <html>
            <body style="font-family: arial;width:600px;color:#757575;">
                <h2 style="color:#0084B6"> Hi '.$data["nodeData"]["field_first_name"][0]["value"].' '.$data["nodeData"]["field_last_name"][0]["value"].',</h2>
                <p style="font-size:16px;">
                    Bo registracion ta reprograma di <strong> '. $data['oldTourDate'] .'</strong> 
                    pa <strong> '. $data['newTourDate'] . ' </strong>
                </p>

                <h4><strong>Informacion di registracion:</strong></h4>
                <table rules="all" style="border-color: #666;" cellpadding="10">
                <tr>
                    <td><strong>Tipo di Tour</strong></td>
                    <td>'. $data["tourTypeSettings"]["title"][0]["value"] .'</td>
                </tr>
                <tr>
                    <td><strong>Fecha y Ora di Tour</strong></td>
                    <td>'. $data["nodeData"]["field_tour_date"][0]["value"] .'</td>
                </tr>
                <tr>
                    <td><strong>Nomber</strong></td>
                    <td>'. $data["nodeData"]["field_first_name"][0]["value"] .'</td>
                </tr>
                <tr>
                    <td><strong>Fam</strong></td>
                    <td>'. $data["nodeData"]["field_last_name"][0]["value"] .'</td>
                </tr>
                <tr>
                    <td><strong>Fecha di Nacemento</strong></td>
                    <td>'. $data["nodeData"]["field_date_of_birth"][0]["value"] .'</td>
                </tr>
                <tr>
                    <td><strong>Address</strong></td>
                    <td>'. $data["nodeData"]["field_address"][0]["value"] .'</td>
                </tr>
                <tr>
                    <td><strong>Telefoon</strong></td>
                    <td>'. $data["nodeData"]["field_phone"][0]["value"] .'</td>
                </tr>
                <tr>
                    <td><strong>E-mail</strong></td>
                    <td>'. $data["nodeData"]["field_email"][0]["value"] .'</td>
                </tr>
            </table>
            <br>
                <p style="font-size:18px;">
                    ' . $data["tourTypeSettings"]["field_email_approved_text"][0]["value"] . '
                </p>
                '. $link_cancel_registration .'
                '. $email_footer .'
            </body>
        </html>
    ';
}

if($type=='delete' && $data['registration']['lang'] == 'en'){
    $registration_canceled_template_client = '
        <html>
            <body style="font-family: arial;width:600px;color:#757575;">
                <h2 style="color:#0084B6"> Hi '.$data["registration"]["firstName"].' '.$data["registration"]["lastName"].',</h2>
                <p style="font-size:16px;">
                    Your registration for tour date <strong>'. $data['registration']['tour_date_display'].'</strong> has been canceled.
                </p>

                '. $email_footer .'
            </body>
        </html>
    ';
}

if($type=='delete' && $data['registration']['lang'] == 'pap'){
    $registration_canceled_template_client = '
        <html>
            <body style="font-family: arial;width:600px;color:#757575;">
                <h2 style="color:#0084B6"> Hi '.$data["registration"]["firstName"].' '.$data["registration"]["lastName"].',</h2>
                <p style="font-size:16px;">
                    Bo registracion pa e tour riba fecha di <strong>'. $data['registration']['tour_date_display'].'</strong> a wordo cancela.
                </p>

                '. $email_footer .'
            </body>
        </html>
    ';
}

if($type=='delete_by_client' && $data['field_language'][0]['value'] == 'en'){
    $registration_canceled_template_by_client = '
        <html>
            <body style="font-family: arial;width:600px;color:#757575;">
                <h2 style="color:#0084B6"> Hi '.$data["field_first_name"][0]['value'].' '.$data["field_last_name"][0]["value"].',</h2>
                <p style="font-size:16px;">
                    Your registration for tour date <strong>'. $data["field_tour_date"][0]["value"] .'</strong> has been canceled by you. <br>
                    If you did NOT cancel your registration, please contact WEB Aruba at 525-4600.
                </p>

                '. $email_footer .'
            </body>
        </html>
    ';
}

if($type=='delete_by_client' && $data['field_language'][0]['value'] == 'pap'){
    $registration_canceled_template_by_client = '
        <html>
            <body style="font-family: arial;width:600px;color:#757575;">
                <h2 style="color:#0084B6"> Hi '.$data["field_first_name"][0]['value'].' '.$data["field_last_name"][0]["value"].',</h2>
                <p style="font-size:16px;">
                    Bo registracion pa fecha di tour <strong>'. $data["field_tour_date"][0]["value"] .'</strong> a keda cancela pa bo persona. <br>
                    Si bo persona NO a cancela e registracion, por fabor tuma contacto cu WEB Aruba na tel. 525-4600.
                </p>

                '. $email_footer .'
            </body>
        </html>
    ';
}

if($type=='delete_by_client_admin'){
    $registration_canceled_template_by_client_admin = '
        <html>
            <body style="font-family: arial;width:600px;color:#757575;">
                <p style="font-size:16px;">
                    Client canceled the following registration:
                </p>
    
                <h4><strong>Registration Info:</strong></h4>
                <table rules="all" style="border-color: #666;" cellpadding="10">
                <tr>
                    <td><strong>ID</strong></td>
                    <td>'. $data["id"][0]["value"] .'</td>
                </tr>
                <tr>
                    <td><strong>Tour Type</strong></td>
                    <td>'. $data["tourTypeSettings"]["title"][0]["value"] .'</td>
                </tr>
                <tr>
                    <td><strong>Tour Date/ Time</strong></td>
                    <td>'. $data["field_tour_date"][0]["value"] .'</td>
                </tr>
                <tr>
                    <td><strong>First Name</strong></td>
                    <td>'. $data["field_first_name"][0]["value"] .'</td>
                </tr>
                <tr>
                    <td><strong>Last Name</strong></td>
                    <td>'. $data["field_last_name"][0]["value"] .'</td>
                </tr>
                <tr>
                    <td><strong>Date of Birth</strong></td>
                    <td>'. $data["field_date_of_birth"][0]["value"] .'</td>
                </tr>
                <tr>
                    <td><strong>Address</strong></td>
                    <td>'. $data["field_address"][0]["value"] .'</td>
                </tr>
                <tr>
                    <td><strong>Phone</strong></td>
                    <td>'. $data["field_phone"][0]["value"] .'</td>
                </tr>
                <tr>
                    <td><strong>Email</strong></td>
                    <td>'. $data["field_email"][0]["value"] .'</td>
                </tr>
            </table>
                '. $email_footer .'
            </body>
        </html>
    ';
}
