<?php

include 'partials-templates.php';
$registration_created_template_client = '';

if($data['lang']=='en'){
    $registration_created_template_client = '
    <html>
        <body style="font-family: arial;width:600px;color:#757575;">
            <h2 style="color:#0084B6"> Hi '.$data['regsitrationFields']['firstName'].' '.$data['regsitrationFields']['lastName'].',</h2>
            <p style="font-size:16px;">
                Your registration request has been received.
            </p>
            <p style="font-size:16px;">
                A follow up email will be sent once your tour registration has been approved by a tour representative.
            </p>
            <p style="font-size:16px;">
                Have a great day!
            </p>

            '. $email_footer .'
        </body>
    </html>';
}else{
    $registration_created_template_client = '
    <html>
        <body style="font-family: arial;width:600px;color:#757575;">
            <h2 style="color:#0084B6"> Bon dia '.$data['regsitrationFields']['firstName'].' '.$data['regsitrationFields']['lastName'].',</h2>
            <p style="font-size:16px;">
                Nos a ricibi bo peticion pa registracion.
            </p>
            <p style="font-size:16px;">
                Lo manda un otro e-mail na momento cu un representante di tour aproba bo registracion di tour oficialmente.
            </p>
            <p style="font-size:16px;">
            Pasa un bon dia!
            </p>

            '. $email_footer .'
        </body>
    </html>';   
}


$registration_created_template_admin = '
    <html>
        <body style="font-family: arial;width:600px;color:#757575;">
            <h2 style="color:#0084B6">Registration Created</h2>

            <table rules="all" style="border-color: #666;" cellpadding="10">
                <tr>
                    <td><strong>Tour Type</strong></td>
                    <td>'. $data["tourTypeSettingsAdmin"]["title"] .'</td>
                </tr>
                <tr>
                    <td><strong>Tour Date</strong></td>
                    <td>'. $data["selectedDate"] .'</td>
                </tr>
                <tr>
                    <td><strong>First Name</strong></td>
                    <td>'. $data["regsitrationFields"]["firstName"] .'</td>
                </tr>
                <tr>
                    <td><strong>Last Name</strong></td>
                    <td>'. $data["regsitrationFields"]["lastName"] .'</td>
                </tr>
                <tr>
                    <td><strong>Date of Birth</strong></td>
                    <td>'. $data["regsitrationFields"]["dob"] .'</td>
                </tr>
                <tr>
                    <td><strong>Address</strong></td>
                    <td>'. $data["regsitrationFields"]["address"] .'</td>
                </tr>
                <tr>
                    <td><strong>Phone</strong></td>
                    <td>'. $data["regsitrationFields"]["phone"] .'</td>
                </tr>
                <tr>
                    <td><strong>Email</strong></td>
                    <td>'. $data["regsitrationFields"]["email"] .'</td>
                </tr>
                <tr>
                    <td><strong>Created time</strong></td>
                    <td>'. $data["dateTimeCreated"] .'</td>
                </tr>

            </table>
            <br>
            '. $link_registration_admin .'
            '. $email_footer .'
        </body>
    </html>
';
