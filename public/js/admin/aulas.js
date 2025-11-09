$(document).ready(function () {

     // --- LECTURA DE DATOS DESDE EL HTML ---
     var $table = $('#aulas-table');
     var datatableLangUrl = $table.data('datatable-lang-url');
     var urlStore = $table.data('url-store');
     var urlEditBase = $table.data('url-edit-base'); // Será /admin/aulas

     // --- INICIALIZACIÓN DE DATATABLES ---
     var table = $table.DataTable({
          dom: 'lBfrtip',
          buttons: [
               { extend: 'excelHtml5', text: 'Exportar a Excel', className: 'btn btn-success' },
               { extend: 'pdfHtml5', text: 'Exportar a PDF', className: 'btn btn-danger', orientation: 'landscape' }
          ],
          language: {
               url: datatableLangUrl // Usa la variable
          }
     });

     // --- LÓGICA DE FILTROS DE LA TABLA ---
     const CAMPUS_COL = 1;
     const EDIFICIO_COL = 2;
     var $edificioWrapper = $('#filtro-edificio-wrapper');
     var $edificioSelect = $('#filtro-edificio');

     $('#filtro-campus').on('change', function () {
          var campusSeleccionado = this.value;

          // 1. Filtra la tabla (como antes)
          table.column(CAMPUS_COL).search(campusSeleccionado).draw();

          // 2. Lógica para mostrar/ocultar el filtro de edificio
          if (campusSeleccionado === 'costa' || campusSeleccionado === 'guayaquil') {
               $edificioWrapper.hide(); // Oculta el filtro
               $edificioSelect.val(''); // Limpia la selección
               table.column(EDIFICIO_COL).search('').draw(); // Limpia el filtro de la tabla
          } else {
               // Esto cubre 'sambo' y 'Todos los Campus' ('')
               $edificioWrapper.show(); // Muestra el filtro de edificio
          }
     });

     $('#filtro-edificio').on('change', function () {
          table.column(EDIFICIO_COL).search(this.value).draw();
     });

     $('#btn-limpiar-filtros').on('click', function () {
          // Limpia los <select>
          $('#filtro-campus').val('');
          $edificioSelect.val('');

          // Limpia los filtros de la tabla
          table.search('').columns().search('').draw();

          // Asegúrate de que el filtro de edificio sea visible al limpiar
          $edificioWrapper.show();
     });

     // --- LÓGICA DEL FORMULARIO MODAL (DROPDOWNS DEPENDIENTES) ---
     $('#form-campus').on('change', function () {
          var campus = $(this).val();
          var $edificioWrapper = $('#edificio-wrapper');
          var $edificioSelect = $('#form-edificio');
          if (campus === 'sambo') {
               $edificioWrapper.show();
               $edificioSelect.prop('required', true);
          } else {
               $edificioWrapper.hide();
               $edificioSelect.prop('required', false);
               $edificioSelect.val('');
          }
     });

     // --- LÓGICA DEL MODAL (CREAR vs EDITAR) ---
     var aulaModal = new bootstrap.Modal(document.getElementById('aulaModal'));
     var $form = $('#aula-form');

     // 1. Al hacer clic en "Crear Nueva Aula"
     $('#btn-crear-aula').on('click', function () {
          $form[0].reset();

          $('#aulaModalLabel').text('Crear Nueva Aula');
          $form.attr('action', urlStore); // Usa la variable
          $('#form-method-spoof').html(''); // Sin método spoof (usa POST)

          $('#form-campus').trigger('change');

          aulaModal.show();
     });

     // 2. Al hacer clic en "Editar" en la tabla
     $('#aulas-table').on('click', '.btn-edit', function () {
          var aulaId = $(this).data('id');

          // Petición AJAX para obtener los datos del aula
          $.get(urlEditBase + '/' + aulaId + '/edit', function (aula) {

               $('#aulaModalLabel').text('Editar Aula: ' + aula.nombreAula);
               $form.attr('action', urlEditBase + '/' + aulaId); // Ruta de 'update'

               // Reemplaza @method('PUT') con el HTML real
               $('#form-method-spoof').html('<input type="hidden" name="_method" value="PUT">');

               $('#form-campus').val(aula.campus);
               $('#form-nombre').val(aula.nombreAula);

               $('#form-campus').trigger('change');
               $('#form-edificio').val(aula.edificio);

               aulaModal.show();
          })
               .fail(function () {
                    alert('Error: No se pudieron cargar los datos del aula.');
               });
     });
});