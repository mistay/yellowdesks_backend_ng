<script>
    var googlemapsapikey = '<?= $googlemapsapikey ?>';
</script>
<?= $this->Html->css('signup.css') ?>
<?= $this->Html->css('mapmarker.css') ?>

<?= $this->Html->script('signup.js') ?>
    
<h2><?= __("Hello - glad you're here! You'd like to rent your desk? Great! Let's do it!") ?></h2>

<div class="signup flexbox">
    <div class="description">
    <?= __("Your profile stores your business data and represents your Yellow Desks. You can use it to advertise your Yellow Desks and manage your earnings.") ?>
    </div>
    <div class="signupform">
        <form name="form1" method="post">
        <input type="hidden" name="_csrfToken" autocomplete="off" value="<?= $this->request->getAttribute('csrfToken') ?>" />
            <div id="step1">
                <h3>Your personal data</h3>
                <table id="table1">
                    <tr>
                        <td><label for="name">Company Name</label></td>
                        <td><label for="firstname">First Name</label></td>
                    </tr>
                    <tr>
                        <td><input type="text" name="name" id="name" placeholder="Company Name" value="<?= @$data['name'] ?>"  />
                        
                        <td><input type="text" name="firstname" placeholder="First Name" value="<?= @$data['firstname'] ?>" /></td>
                    </tr>
                    <tr class="errorline">
                        <td>
                            <span class="check name"><?= __("Please note that Yellow Desks is a B2B service.") ?></span></td>
                        </td>
                    </tr>
                    <tr class="space">
                        <td><label for="lastname">Last Name</label></td>
                        <td><label for="email">E-Mail</label></td>
                    </tr>
                    
                    <tr>
                        <td><input type="text" name="lastname" placeholder="Last Name" value="<?= @$data['lastname'] ?>" /></td>
                        <td><input type="text" name="email" placeholder="E-Mail" value="<?= @$data['email'] ?>" /></td>
                    </tr>
                    <tr class="space">
                        <td><label for="address"><?= __("Billing Address (will be put on your invoice)") ?></label></td>
                    </tr>
                    <tr class="inputs">
                        <td colspan="2"><input type="text" name="address" id="address" placeholder="Billing Address (will be put on your invoice)" value="<?= @$data['address'] ?>" /></td>
                    </tr>
                    
                    <tr class="space">
                        <td><label for="postal_code">Postal Code</label></td>
                        <td><label for="city">City</label></td>
                    </tr>
                    <tr class="inputs">
                        <td><input type="text" name="postal_code" id="postal_code" placeholder="Postal Code" value="<?= @$data['postal_code'] ?>" /></td>
                        <td><input type="text" name="city" id="city" placeholder="City" value="<?= @$data['city'] ?>" /></td>
                    </tr>

                    <tr class="space">
                        <td colspan="2"><label for="password">Password</label></td>
                    </tr>
                    <tr class="inputs">
                        <td colspan="2"><input type="password" name="password" id="password" placeholder="Password" value="<?= @$data['password'] ?>" /></td>
                    </tr>
                    <tr class="errorline">
                        <td colspan="2">
                            <span class="check password"><?= __("Please make sure your password is at least 8 characters long") ?></span></td>
                        </td>
                    </tr>

                    

                    <tr class="inputs">
                        
                        <td><h3>Your Yellowdesk(s)</h3><br /><label for="desks">Number of Desks, e.g. 2</label></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td><input type="text" name="desks" id="desks" placeholder="Number of Desks, e.g. 2" value="<?= @$data['desks'] ?>"  />
                        <td></td>
                    </tr>
                    <tr class="space">
                        <td colspan="2"><label for="title">Title, e.g. Beautiful office space downtown</label></td>
                    </tr>
                    <tr class="inputs">
                        <td colspan="2"><input type="text" name="title" id="title" placeholder="Title, e.g. Beautiful office space downtown" value="<?= @$data['title'] ?>" /></td>
                    </tr>
                    <tr class="space">
                        <td colspan="2"><label for="details">Included, e.g. Coffee, B/W A4 Printer, WiFi, Telephone Room</label></td>
                    </tr>
                    <tr class="inputs">
                        <td colspan="2"><input type="text" name="details" id="details" placeholder="Included, e.g. Coffee, B/W A4 Printer, WiFi, Telephone Room." value="<?= @$data['details'] ?>" /></td>
                    </tr>
                    <tr class="errorline">
                        <td>
                            <span class="check name"><?= __("Please note: for office space minimum requirements are: connectivity (i.e. WIFI, LAN, ..), desk and chair.") ?></span></td>
                        </td>
                    </tr>
                    <tr class="inputs">
                        <td colspan="2"><label for="extras">Excluded, e.g. Parking lot, High-Speed Wifi, Conference Room</label></td>
                    </tr>
                    <tr class="inputs">
                        <td colspan="2"><input type="text" name="extras" id="extras" placeholder="Excluded, e.g. Parking lot, High-Speed Wifi, Conference Room" value="<?= @$data['extras'] ?>" /></td>
                    </tr>
                    <tr class="inputs">
                        <td colspan="2"><label for="addressyellowdesk">Yellowdesks Address (will be shown to your coworkers after booking), e.g. Jakob-Haringer-Str. 3, 5020 Salzburg</label></td>
                    </tr>
                    <tr class="inputs">
                        <td colspan="2"><input type="text" name="addressyellowdesk" id="addressyellowdesk" 
                            placeholder="Yellowdesks Address (will be shown to your coworkers after booking), e.g. Jakob-Haringer-Str. 3, 5020 Salzburg" value="<?= @$data['addressyellowdesk'] ?>" />
                            <input type="button" id="lookup" value="<?= __("Move yellow pin in map to this location") ?>" />
                        </td>
                    </tr>
                    <tr class="inputs">
                        <td colspan="2"><label for="addressadditional">Additional information about how to find the entrance of the Yellowdesk(s), e.g. Please go to the glass door as the main entrance door of building No. 2, then follow yellow arrows on the floor.</label></td>
                    </tr>
                    <tr class="inputs">
                        <td colspan="2"><input type="text" name="addressadditional" id="addressadditional" 
                            placeholder="Additional information about how to find the entrance of the Yellowdesk(s), e.g. Please go to the glass door as the main entrance door of building No. 2, then follow yellow arrows on the floor." value="<?= @$data['addressadditional'] ?>" />
                    </td>
                    </tr>
                    <tr class="space">
                        <td><label for="ydaddress"><?= __("Please drag the marker to the excact entrance of your worspace where you have your Yellowdesk(s)") ?></label></td>
                    </tr>
                    <tr class="inputs">
                        <td colspan="2"><input type="text" id="pac-input" /></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="maptarget"></div>
                            <div id="map" style="width: 100%; height: 250px;"></div>
                        </td>
                    </tr>
                    
                    
                    <tr class="space">
                        <td><input type="checkbox" value="yes" name="termsandconditions" />
                            <label for="termsandconditions"><?= __("I agree to <a target='_blank' href={0}>Terms & Conditions</a>", $this->Url->build(["controller" => "termsandconditions", "action" => "index"])); ?></label></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td class="right">
                        <input readonly="readonly" type="hidden" name="lat" id="lat" placeholder="Lat" value="<?= @$data['lat'] ?>" />
                        <input readonly="readonly" type="hidden" name="lng" id="lng" placeholder="Lng" value="<?= @$data['lng'] ?>" />
                    </tr>
                    <tr>
                        <td><input type="submit" id="finish" value="<?= __("Finish") ?>" /></td>
                    </tr>
                </table>
            </div>
        </form>
    </div>
</div>

<?= $this->Html->script('mapmarker.js') ?>

<script>
    $(window).on('positionchanged', function (e) {
        // console.log("event " + e.state.lat + "/" + e.state.lng);
        $("#lat").val(e.state.lat);
        $("#lng").val(e.state.lng);
    });
</script>

<script type="text/javascript">
    $("#lookup").on('click', function() {
        console.log($('#addressyellowdesk').val());
        
        var geocoder =  new google.maps.Geocoder();
    
        geocoder.geocode( { 'address': $('#addressyellowdesk').val()}, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            setPosition(results[0].geometry.location.lat(), results[0].geometry.location.lng());
            moveMarker();
            setCenter();
            //console.log("bla" + results[0].geometry.location.lat() + " " +results[0].geometry.location.lng());
        }
        });
    });
 </script>