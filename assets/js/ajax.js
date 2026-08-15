/* ============================================================
   AIRLINE TICKETING SYSTEM
   AJAX Field Validation (XMLHttpRequest ONLY — no fetch, no JSON)
   File: assets/js/ajax.js
   ============================================================ */

function checkField(field, value, hintId, mode) {
    if (value.length === 0) {
        document.getElementById(hintId).innerHTML = "";
        return;
    }
    const xmlhttp = new XMLHttpRequest();
    xmlhttp.onload = function () {
        document.getElementById(hintId).innerHTML = this.responseText;
    };
    let url = "validate_passenger.php?field=" + field + "&value=" + encodeURIComponent(value) + "&mode=" + (mode || "format");
    xmlhttp.open("GET", url);
    xmlhttp.send();
}