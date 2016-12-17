function SoloNumeros(ctrl) {
    $(ctrl).keydown(function (event) {
        if (event.shiftKey) {
            event.preventDefault();
        }

        if (event.keyCode === 46 || event.keyCode === 9) {
        }
        else {
            if (event.keyCode < 95) {
                if (event.keyCode < 48 || event.keyCode > 57) {
                    event.preventDefault();
                }
            }
            else {
                if (event.keyCode < 96 || event.keyCode > 105) {
                    event.preventDefault();
                }
            }
        }
    });
}
function ValidaFecha(fecha) { 
    var patron=new RegExp("^([0-9]{1,2})([/])([0-9]{1,2})([/])(19|20)+([0-9]{2})$"); 
    if(fecha.search(patron)=== 0) 
    { var values=fecha.split("/"); 
        if(isValidDate(values[0],values[1],values[2])) 
        { 
            return true; 
        } 
    } 
    return false; 
} 
function CalcularDias(fechaInicial, fechaFinal, tarctrl){ 
    if(ValidaFecha(fechaInicial) && ValidaFecha(fechaFinal)) 
    { 
        inicial=fechaInicial.split("/"); final=fechaFinal.split("/"); 
        var dateStart=new Date(inicial[2],(inicial[1]-1),inicial[0]); 
        var dateEnd=new Date(final[2],(final[1]-1),final[0]); 
        if(dateStart<dateEnd) 
        { 
            +(((dateEnd - dateStart) / 86400) / 1000) + " días";
        }
        else {
            resultado = "La fecha inicial es posterior a la fecha final";
        }
    }
    else {
        if (!ValidaFecha(fechaInicial)) resultado = "La fecha inicial es incorrecta";
        if (!ValidaFecha(fechaFinal)) resultado = "La fecha final es incorrecta";
    }
    $(tarctrl).val(resultado);
}
function HistoryBack() {
    window.history.back();
}
function DiasEntre(fechainicial, fechafinal){
    var fecini = fechainicial.split('/');
    var fecfin = fechafinal.split('/');
    return Math.floor(Date.parse(fecfin[1]+'/'+fecfin[0]+'/'+fecfin[2]) - Date.parse(fecini[1]+'/'+fecini[0]+'/'+fecini[2])) / 86400000;
}
function ValidateRequiredFields(){
    var ctrls = $("input[type='text']");
    var messages='';
    $.each(ctrls, function(i, ctrl){
        if((ctrl.attributes['data-required'] != null) && ctrl.value.trim() === ''){
            messages += ctrl.attributes['data-required'].value + '.\n';
        }
    });
    if(messages.trim() !== '')
    {
        alert(messages);
        return false;
    }
    else{return true;}
}
function getUrlParam(name) {
    var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
    alert(window.location.href);
    if (results == null) {
        return null;
    }
    else {
        return results[1] || 0;
    }
}
(function ($) {
    /// Test
    var shade = "#556b2f";
    $.fn.greenify = function () {
        this.css("color", shade);
        return this;
    };
    ///
    $.fn.DateTimePicker = function () {
        this.datetimepicker({
            datepicker: true,
            format: 'd/m/Y H:i',
            step: 5
        });
        return this;
    };
    ///
    $.fn.CurrentDateTime = function(){
        var currentdate = new Date(); 
        var datetime = 
                currentdate.getDate() + "/"
                + (currentdate.getMonth()+1)  + "/" 
                + currentdate.getFullYear() +   " "
                + currentdate.getHours() + ":"  
                + currentdate.getMinutes() ;
        this.val(datetime);
        return this;
    };

}(jQuery));
///
SoloNumeros($('input[data-type="number"]'));
//$('input[data-type="number"]').val(0);
$('input[data-type="datetime"]').DateTimePicker();
