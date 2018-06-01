$(document).ready(function(){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
});

var hexDigits = new Array ("0","1","2","3","4","5","6","7","8","9","a","b","c","d","e","f"); 

//Function to convert rgb color to hex format
function rgb2hex(rgb) {
    rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
    return "#" + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
}

function hex(x) {
    return isNaN(x) ? "00" : hexDigits[(x - x % 16) / 16] + hexDigits[x % 16];
}
  
$('.refreshstable').on('click',function(){
tableid= $(this).parents('.ibox-content').find('table').attr('id');
$("#"+tableid).DataTable().search('').draw();
});

function setToastNotification(objDataParams) {
   
    var header = "Codemine";
    if(typeof objDataParams.header != 'undefined') {
        header = objDataParams.header;
    }

    var message = "Welcome!!";
    if(typeof objDataParams.message != 'undefined') {
        message = objDataParams.message;
    }

    setTimeout(function() {
        toastr.options = {
            closeButton: true,
            progressBar: true,
            showMethod: 'slideDown',
            timeOut: 4000
        };
        toastr.success(header, message);

    }, 1000);
}

function removeElementFromArray(array, index) {
    
    //var arrarIndex = parseInt(parseInt(index) - 1);

    for(var i = array.length - 1; i >= 0; i--) {
        if(i === index) {
            array.splice(i, 1);
        }
    }

    return array;
}