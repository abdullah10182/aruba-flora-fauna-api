<?php

include 'partials-templates.php';

$email_assigned_worker_template = '
    <html>
        <body style="font-family: arial;width:600px;color:#757575;">
            <h2 style="color:#0084B6">Order assigned to you.</h2>

            <table rules="all" style="border-color: #666;" cellpadding="10">
                <tr>
                    <td><strong>Order Number</strong></td>
                    <td>'. $data["title"].'</td>
                </tr>
                <tr>
                    <td><strong>Order State</strong></td>
                    <td>'. $data["order_state_name"].'</td>
                </tr>
                <tr>
                    <td><strong>Service date and time</strong></td>
                    <td>'. $data["service_date_time_formatted"] .'</td>
                </tr>
                <tr>
                    <td><strong>Company</strong></td>
                    <td>'. $data["field_company_name"] .'</td>
                </tr>
                <tr>
                    <td><strong>Company address</strong></td>
                    <td>'. $data["field_address"] .'</td>
                </tr>
                <tr>
                    <td><strong>Contact person</strong></td>
                    <td>'. $data["field_name"] . ' ' . $data['field_last_name'] . '</td>
                </tr>
                <tr>
                    <td><strong>Phone</strong></td>
                    <td>'. $data["field_phone_number"] .'</td>
                </tr>
                <tr>
                    <td><strong>Email</strong></td>
                    <td>'. $data["mail"] .'</td>
                </tr>

            </table>
            <br>
            '. $button_link_admin_edit .'
            '. $email_footer .'
        </body>
    </html>
';
