// $Id: $
function zf_addDataToSalesIQ() {
    var visitorinfo = {};
    var elements = document.getElementById("form").elements;
    for (var elmIdx = 0; elmIdx < elements.length; elmIdx++) {
        var inpElem = elements[elmIdx];
        var fieldLinkName = inpElem.getAttribute("name");
        var fieldType = inpElem.getAttribute("fieldType");
        if (fieldType === "1" || fieldType === "7" || fieldType === "9" || fieldType === "11") {
            for (var idx = 0; idx < salesIQFieldsArray.length; idx++) {
                var salesIQObj = salesIQFieldsArray[idx];
                var zfFieldName = salesIQObj.formFieldName;
                if (fieldType === "1") {
                    if (fieldLinkName === zfFieldName) {
                        var salesIQValue = inpElem.value;
                        var salesIQFieldName = salesIQObj.salesIQFieldName;
                        if (salesIQFieldName === "Phone") {
                            visitorinfo.contactnumber = salesIQValue;
                        }
                        if (salesIQFieldName === "Name") {
                            visitorinfo.name = salesIQValue;
                        }
                    }
                }
                if (fieldType === "7") {
                    var zfFldCompLinkName = salesIQObj.fieldCompLinkName;
                    if (fieldLinkName === zfFldCompLinkName) {
                        var salesIQValue = inpElem.value;
                        visitorinfo.name = salesIQValue;
                    }
                }
                if (fieldType === "9") {
                    if (fieldLinkName === zfFieldName) {
                        var salesIQValue = inpElem.value;
                        visitorinfo.email = salesIQValue;
                    }
                }
                if (fieldType === "11") {
                    fieldLinkName = inpElem.getAttribute("compname");
                    if (fieldLinkName === zfFieldName) {
                        var phoneFormat = inpElem.getAttribute("phoneFormat");
                        var salesIQValue = "";
                        if (phoneFormat === "1") {
                            var isCodeEnabled = inpElem.getAttribute("isCountryCodeEnabled");
                            salesIQValue = document.getElementById("international_" + fieldLinkName + "_countrycode").value;
                            if (salesIQValue != null && salesIQValue != "" && isCodeEnabled === "true") {
                                salesIQValue = document.getElementById("international_" + fieldLinkName + "_countrycodeval").value + salesIQValue;
                            }
                        } else if (phoneFormat === "2") {
                            var countryCode = document.getElementById(fieldLinkName + "_countrycode").value;
                            var first = document.getElementById(fieldLinkName + "_first").value;
                            var last = document.getElementById(fieldLinkName + "_second").value;
                            salesIQValue = countryCode + first + last;
                        }
                        visitorinfo.contactnumber = salesIQValue;
                    }
                }
            }
        }
    }
    parent.postMessage(JSON.stringify({type: 'zoho.salesiq.apimessage', visitor: visitorinfo}), '*');// No I18N
}
