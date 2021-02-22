<h2>Booking Confirmation</h2>


<?php $rawrequest = json_decode($booking -> paypalipn -> rawrequest, true); ?>
<table>
    <tr>
        <th>Host</th>
        <td><?= $booking -> host -> nickname ?><br /><?= $booking -> host -> name ?><br /><?= $booking -> host -> firstname ?> <?= $booking -> host -> lastname ?></td>
    </tr>
    <tr>
        <th>Host's email address</th>
        <td><a href="mailto:<?= $booking -> host -> email ?>"><?= $booking -> host -> email ?></a></td>
    </tr>
    <tr>
        <th>Host includes</th>
        <td><?= $booking -> host -> details ?></td>
    </tr>
    <tr>
        <th>Host's opening hours</th>
        <td>
            Mon: <?= $booking -> host -> open_monday_from ->i18nFormat('HH:mm') ?> - <?= $booking -> host -> open_monday_till ->i18nFormat('HH:mm') ?><br />
            Tue: <?= $booking -> host -> open_tuesday_from ->i18nFormat('HH:mm') ?> - <?= $booking -> host -> open_tuesday_till ->i18nFormat('HH:mm') ?><br />
            Wed: <?= $booking -> host -> open_wednesday_from ->i18nFormat('HH:mm') ?> - <?= $booking -> host -> open_wednesday_till ->i18nFormat('HH:mm') ?><br />
            Thu: <?= $booking -> host -> open_thursday_from ->i18nFormat('HH:mm') ?> - <?= $booking -> host -> open_thursday_till ->i18nFormat('HH:mm') ?><br />
            Fri: <?= $booking -> host -> open_friday_from ->i18nFormat('HH:mm') ?> - <?= $booking -> host -> open_friday_till ->i18nFormat('HH:mm') ?><br />
            Sat: <?= $booking -> host -> open_saturday_from ->i18nFormat('HH:mm') ?> - <?= $booking -> host -> open_saturday_till ->i18nFormat('HH:mm') ?><br />
            Sun: <?= $booking -> host -> open_sunday_from ->i18nFormat('HH:mm') ?> - <?= $booking -> host -> open_sunday_till ->i18nFormat('HH:mm') ?>
        </td>
    </tr>
    <tr>
        <th>Host's 24/7 member access</th>
        <td><?= $booking -> host -> open_sunday_from ? "yes" : "no" ?></td>
    </tr>
    <tr>
        <th>Host's cancellation scheme</th>
        <td><?= $booking -> host -> cancellationscheme ?></td>
    </tr>
    <tr>
        <th>Host excludes</th>
        <td><?= $booking -> host -> extras ?></td>
    </tr>
    <tr>
        <th>Host's phone number</th>
        <td><?= $booking -> host -> phone ?></td>
    </tr>
    <tr>
        <th>Booking Start Date (including)</th>
        <td><?= $booking -> begin ?></td>
    </tr>
    <tr>
        <th>Booking End Date (including)</th>
        <td><?= $booking -> end ?></td>
    </tr>
    <tr>
        <th>Opening instructions</th>
        <td><?= $booking -> host -> openinginstructions ?></td>
    </tr>
    <tr>
        <th>Exact GPS Coordinates</th>
        <td>Lat: <?= $booking -> host -> lat ?>, Lng: <?= $booking -> host -> lng ?><br />
        <a target="_blank" href="http://www.google.com/maps/place/<?= $booking -> host -> lat ?>,<?= $booking -> host -> lng ?>">Open in Google Maps</a>
        </td>
    </tr>
    <tr>
        <th>Payment received</th>
        <td><?= $booking -> paypalipn -> ts_inserted -> i18nFormat('yyyy-MM-dd HH:mm:ss') ?></td>
    </tr>
    <tr>
        <th>Payment subject</th>
        <td><?= $rawrequest["item_name"] ?></td>
    </tr>
    <tr>
        <th>Payment amount</th>
        <td><?= $booking -> paypalipn -> mc_gross ?> <?= $rawrequest["mc_currency"] ?></td>
    </tr>
    <tr>
        <th>Payment paypal transaction id</th>
        <td><?= $booking -> paypalipn -> txn_id ?></td>
    </tr>
    <tr>
        <th>Yellowdesks booking ids</th>
        <td><?= $booking -> paypalipn -> custom ?></td>
    </tr>
     <tr>
        <th>Payment payer email</th>
        <td><?= $rawrequest["payer_email"] ?></td>
    </tr>
</table>