$(document).ready(function () {
    
    $('.dataTables-example').DataTable({
        'aoColumnDefs': [{
            'bSortable': false,
            'aTargets': ['nosort']
        }]
//        dom: '<"html5buttons"B>lTfgitp',
//        buttons: [
//            {extend: 'copy'},
//            {extend: 'csv'},
//            {extend: 'excel', title: 'ExampleFile'},
//            {extend: 'pdf', title: 'ExampleFile'},
//
//            {extend: 'print',
//                customize: function (win) {
//                    $(win.document.body).addClass('white-bg');
//                    $(win.document.body).css('font-size', '10px');
//
//                    $(win.document.body).find('table')
//                            .addClass('compact')
//                            .css('font-size', 'inherit');
//                }
//            }
//        ]

    });
});