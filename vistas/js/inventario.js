/*=============================================
EDITAR CATEGORIA
=============================================*/

$(".tablas").on("click", ".btnEditarCategoria", function(){

	var idCategoria = $(this).attr("idCategoria");
	
	var datos = new FormData();
	datos.append("idCategoria", idCategoria);

	$.ajax({

		url:"ajax/categorias.ajax.php",
		method: "POST",
		data: datos,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function(respuesta){
			
			$("#editarNombreCategoria").val(respuesta["nombre"]);
			$("#editarDescripcionCategoria").val(respuesta["descripcion"]);
			$("#editarIdCategoria").val(respuesta["id"]);
		}

	});

})

/*=============================================
ELIMINAR CATEGORIA
=============================================*/
$(".tablas").on("click", ".btnEliminarCategoria", function(){

    var idCategoria = $(this).attr("idCategoria");
	var idCategoriaValidar = $(this).attr("idCategoria");
  
	var datos = new FormData();
	datos.append("idCategoriaValidar", idCategoriaValidar);

	$.ajax({

		url:"ajax/productos.ajax.php",
		method: "POST",
		data: datos,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function(respuesta){
			console.log(respuesta);
			 if (respuesta != false) {  // Si la respuesta contiene productos
				swal({
					title: 'No puedes eliminar una categoría que tenga productos',
					text: "¡Cancela la accíón!",
					type: 'warning',
					showCancelButton: true,
					confirmButtonColor: '#3085d6',
					  cancelButtonColor: '#d33',
					  cancelButtonText: 'Cancelar',
					  confirmButtonText: 'Si, cancelar!'
				  }).then(function(result){
				
				  })			
			} else {
				swal({
					title: '¿Está seguro de borrar la categoría?',
					text: "¡Si no lo está puede cancelar la accíón!",
					type: 'warning',
					showCancelButton: true,
					confirmButtonColor: '#3085d6',
					  cancelButtonColor: '#d33',
					  cancelButtonText: 'Cancelar',
					  confirmButtonText: 'Si, borrar categoría!'
				  }).then(function(result){
				
					if(result.value){
				
					  window.location = "index.php?ruta=inventario&idCategoriaEliminar="+idCategoria;
				
					}
				
				  })
			}
		}

	});
  
});

