/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


//Functions
function RegPasEx(){
    var valid = true;
    if ( valid ) {
      $( "#PASEX tbody" ).append( 
        "<tr>" +
          "<td>" + $('#NOM_PASEX').val() + "</td>" +
          "<td>" + $('#TELEFONO_PASEX').val() + "</td>" +
        "</tr>" );
    }
    return valid;
}
function tableToJson(table){
    var jsObj = [],
        tbDataRows = $(table).find('tbody tr');
    if (tbDataRows.length > 0) {
        for (rows = 0; rows < tbDataRows.length; rows++) {
            rowscols = $(tbDataRows).eq(rows).find('td');
            if (rowscols.length > 0) {
                debugger;
                var obj = {};
                for (cols = 0; cols < rowscols.length; cols++) {
                    key = $(table.find('thead tr')[0]).find('th').eq(cols).attr('id');
                    //if (key != null) {
                        value = $(rowscols).eq(cols).text();
                        obj[key] = value;
                    //}
                }
                jsObj.push(obj);
            }
        }
    }
    return JSON.stringify(jsObj);
}
//        var dialog = $('#scrAddPas').dialog({
//            autoOpen: false,
//            height: 280,
//            width: 600,
//            modal: true,
//            buttons: {
//                "Registrar": RegPasEx,
//                Cerrar: function() {
//                    dialog.dialog( "close" );
//                }
//            }
//        });