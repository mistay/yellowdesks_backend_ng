function hideplaceholdershowlabels(elem) {
    $(elem).val() == "" ? $("label[for='" + elem.name + "']").fadeOut() : $("label[for='" + elem.name + "']").fadeIn();
}
$( document ).ready(function() {
    // show labels as soon user provided values
    $("input[type=text], input[type=password], input[type=checkbox]").bind("input change", function() {
        if (revalidate)
            formvalidataion();
        hideplaceholdershowlabels(this);
    });

    // hide labels after startup
    $("input[type=text], input[type=password]").each(function() {
        hideplaceholdershowlabels(this);
    });

    $("#companyname").bind("input", function(){
        $(this).val() == "" ? $(".check.companyname").fadeIn() : $(".check.companyname").fadeOut();
    });

    $("#password").bind("input", function() {
        $(this).val().length < 8 ? $(".check.password").fadeIn() : $(".check.password").fadeOut();
    });

    $("#step2").hide();

    
    $("#next").click(function() {
        revalidate=true;
        if (!formvalidataion()) {
            revalidate = false;
            $("#step1").fadeOut();
            $("#step2").delay(500).fadeIn(400);
        }
        // switch to next but do _not_ submit form
        return false;
    });

    $("#previous").click(function() {
        $("#step2").fadeOut();
        $("#step1").delay(500).fadeIn();
        return false;
    });

    $("#finish").click(function() {
        if (!formvalidataion()) {

        } else {
            // do not submit form
            return false;
        }
    });
});

var revalidate=false;
function formvalidataion() {
    errors = false;
    $("input:visible").each(function() {

        if (this.id == "pac-input") {
            return;
        }

        if ($(this).val() == "") {
            $(this).addClass("error");
            errors = true;
        }
        else
            $(this).removeClass("error");
    });

    $("input[type=checkbox]").each(function() {
        if ($(this).is(':not(:checked)')) {
            errors = true;
            $("label[for="+this.name+"]").addClass("error");
            $(this).addClass("error");
        } else {
            $("label[for="+this.name+"]").removeClass("error");
            $(this).removeClass("error");
        }
    });
    console.log("errors: " + errors);
    return errors;
}