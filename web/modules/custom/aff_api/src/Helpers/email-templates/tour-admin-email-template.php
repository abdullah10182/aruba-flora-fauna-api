<?php

include 'partials-templates.php';

$comments = '';
if(isset($data['comments'])){
    $comments .= '<hr>';
    $comments .= '<h4>Additional Comments:</h4>';
    $comments .= '<p>' . $data['comments'] . '</p>';
}

$tour_admin_email_template = '
    <html>
        <body style="font-family: arial;width:600px;color:#757575;">

            <div style="color:#0084B6;font-size:16px;">Tour Type: ' . $data['tourType'] .'</div>
            <div style="color:#0084B6;font-size:16px;">Tour Date: ' . $data['tourDate'] .'</div>
            <hr>
            <h3 style="color:#0084B6">Registrations</h3>
            <table rules="all" style="border-color: #666; width:100%; font-size:13px" cellpadding="10">
            <tr>
                <th>ID number</th>
                <th>Tour Type</th>
                <th>Full Name</th> 
                <th>Birthdate</th>
                <th>Address</th>
                <th>Phone</th>
                <th>Group Size</th>
            </tr>
                '. $data['tableRegistrations'] .'
            </table>
            <br>
            '. $comments .' 
            <br>
            '. $email_footer .'
        </body>
    </html>
';
