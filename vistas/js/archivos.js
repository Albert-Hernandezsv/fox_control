/*=============================================
DESCARGAR TABLA VENTAS CONSUMIDOR FINAL EN PDF
=============================================*/
function ventasConsumidorFinalPdf() {
	// Mostrar el mensaje de carga con SweetAlert
    swal({
        title: "Generando PDF",
        text: "Por favor espera mientras se genera el archivo.",
        icon: "info",
        showConfirmButton: false,
        closeOnClickOutside: false,
        closeOnEsc: false,
    });
    // Obtener la instancia de DataTable
    const dataTable = $('#anexoVentas').DataTable();

    // Guardar la configuración original
    const originalLength = dataTable.page.len();

    // Mostrar todos los registros en una sola página
    dataTable.page.len(-1).draw();

    // Crear el PDF
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({
        orientation: 'landscape',
    });

    // Obtener los encabezados de las columnas
    // Obtener los encabezados de las columnas
const columns = [];
const totalColumns = $('#anexoVentas thead th').length;

$('#anexoVentas thead th').each(function(index) {
    let text = $(this).text();
    if (index === totalColumns - 2) {
        text = "Renta"; // Cambiar el penúltimo encabezado
    } else if (index === totalColumns - 1) {
        text = "Anexo"; // Cambiar el último encabezado
    }
    columns.push(text);
});



    // Obtener los datos de la tabla
    const rows = [];
    dataTable.rows().every(function() {
        rows.push(this.data());
    });

    doc.autoTable({
        head: [columns],
        body: rows,
        theme: 'grid',
        styles: { fontSize: 6 },
        columnStyles: {
            0: { cellWidth: 10 },
            1: { cellWidth: 10 },
            2: { cellWidth: 10 },
            3: { cellWidth: 20 },
            4: { cellWidth: 20 },
            5: { cellWidth: 10 },
            6: { cellWidth: 10 },
            7: { cellWidth: 20 },
            8: { cellWidth: 20 },
            9: { cellWidth: 10 },
            10: { cellWidth: 10 },
            11: { cellWidth: 15 },
            12: { cellWidth: 10 },
            13: { cellWidth: 15 },
            14: { cellWidth: 15 },
            15: { cellWidth: 15 },
            16: { cellWidth: 10 },
            17: { cellWidth: 15 },
            18: { cellWidth: 20 },
            19: { cellWidth: 10 },
            20: { cellWidth: 10 },
        },
        margin: { top: 10, left: 2, right: 10 },
        pageBreak: 'auto',
        tableWidth: 'auto',
    });

    // Restaurar la configuración original de paginación
    dataTable.page.len(originalLength).draw();

    // Guardar el PDF
    doc.save('ANEXO-VENTAS-FINAL.pdf');
	swal.close()
}

/*=============================================
DESCARGAR TABLA VENTAS CONSUMIDOR FINAL EN EXCEL
=============================================*/
function ventasConsumidorFinalExcel() {
    // Mostrar el mensaje de carga con SweetAlert
    swal({
        title: "Generando archivo",
        text: "Por favor espera mientras se genera el archivo.",
        icon: "info",
        showConfirmButton: false,
        closeOnClickOutside: false,
        closeOnEsc: false,
    });

    // Obtener la instancia de DataTable
    const dataTable = $('#anexoVentas').DataTable();

    // Obtener todos los datos de la tabla, no solo los visibles
    const data = dataTable.rows().data().toArray();

    // Crear un nuevo libro de trabajo con ExcelJS
    const wb = new ExcelJS.Workbook();
    const ws = wb.addWorksheet("Facturas");

    // Obtener los encabezados de la tabla
    const headers = [];
    $('#anexoVentas thead th').each(function() {
        headers.push($(this).text().trim());
    });

    // Agregar los encabezados como primera fila en el Excel
    ws.addRow(headers);

    // Agregar las filas de datos
    ws.addRows(data);

    // Exportar el archivo Excel
    wb.xlsx.writeBuffer().then(function(buffer) {
        const blob = new Blob([buffer], { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = "anexo_de_ventas_final.xlsx";
        link.click();
    });

    // Cerrar el mensaje de carga
    swal.close();
}

/*=============================================
DESCARGAR TABLA VENTAS CONSUMIDOR FINAL EN EXCEL CSV
=============================================*/
function ventasConsumidorFinalCsv() {
    swal({
        title: "Generando archivo",
        text: "Por favor espera mientras se genera el archivo.",
        icon: "info",
        showConfirmButton: false,
        closeOnClickOutside: false,
        closeOnEsc: false,
    });

    const dataTable = $('#anexoVentas').DataTable();
    const allData = dataTable.rows().data();

    let csv = '';
    for (let i = 0; i < allData.length; i++) {
        const row = allData[i];
        const rowData = [];

        for (let j = 0; j < row.length; j++) {
            rowData.push(String(row[j]).replace(/;/g, ',')); // Cambia ; por , si está en el contenido
        }

        csv += rowData.join(';') + '\n';
    }

    const link = document.createElement('a');
    link.href = 'data:text/csv;charset=utf-8,' + encodeURIComponent(csv);
    link.download = 'anexo_de_ventas_final.csv';
    link.click();

    swal.close();
}

/*=============================================
DESCARGAR TABLA VENTAS CONTRIBUYENTES EN PDF
=============================================*/
function ventasContribuyentePdf() {
	// Mostrar el mensaje de carga con SweetAlert
    swal({
        title: "Generando PDF",
        text: "Por favor espera mientras se genera el archivo.",
        icon: "info",
        showConfirmButton: false,
        closeOnClickOutside: false,
        closeOnEsc: false,
    });
    // Obtener la instancia de DataTable
    const dataTable = $('#anexoVentasContribuyentes').DataTable();

    // Guardar la configuración original
    const originalLength = dataTable.page.len();

    // Mostrar todos los registros en una sola página
    dataTable.page.len(-1).draw();

    // Crear el PDF
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({
        orientation: 'landscape',
    });

    // Obtener los encabezados de las columnas
    // Obtener los encabezados de las columnas
const columns = [];
const totalColumns = $('#anexoVentasContribuyentes thead th').length;

$('#anexoVentasContribuyentes thead th').each(function(index) {
    let text = $(this).text();
    if (index === totalColumns - 2) {
        text = "Renta"; // Cambiar el penúltimo encabezado
    } else if (index === totalColumns - 1) {
        text = "Anexo"; // Cambiar el último encabezado
    }
    columns.push(text);
});



    // Obtener los datos de la tabla
    const rows = [];
    dataTable.rows().every(function() {
        rows.push(this.data());
    });

    doc.autoTable({
        head: [columns],
        body: rows,
        theme: 'grid',
        styles: { fontSize: 6 },
        columnStyles: {
            0: { cellWidth: 10 },
            1: { cellWidth: 10 },
            2: { cellWidth: 10 },
            3: { cellWidth: 20 },
            4: { cellWidth: 20 },
            5: { cellWidth: 10 },
            6: { cellWidth: 10 },
            7: { cellWidth: 20 },
            8: { cellWidth: 20 },
            9: { cellWidth: 10 },
            10: { cellWidth: 10 },
            11: { cellWidth: 15 },
            12: { cellWidth: 10 },
            13: { cellWidth: 15 },
            14: { cellWidth: 15 },
            15: { cellWidth: 15 },
            16: { cellWidth: 10 },
            17: { cellWidth: 15 },
            18: { cellWidth: 20 },
            19: { cellWidth: 10 },
        },
        margin: { top: 10, left: 2, right: 10 },
        pageBreak: 'auto',
        tableWidth: 'auto',
    });

    // Restaurar la configuración original de paginación
    dataTable.page.len(originalLength).draw();

    // Guardar el PDF
    doc.save('ANEXO-VENTAS-CONTRIBUYENTES.pdf');
	swal.close()
}

/*=============================================
DESCARGAR TABLA VENTAS CONTRIBUYENTES EN EXCEL
=============================================*/
function ventasContribuyenteExcel() {
    // Mostrar el mensaje de carga con SweetAlert
    swal({
        title: "Generando archivo",
        text: "Por favor espera mientras se genera el archivo.",
        icon: "info",
        showConfirmButton: false,
        closeOnClickOutside: false,
        closeOnEsc: false,
    });

    // Obtener la instancia de DataTable
    const dataTable = $('#anexoVentasContribuyentes').DataTable();

    // Obtener todos los datos de la tabla, no solo los visibles
    const data = dataTable.rows().data().toArray();

    // Crear un nuevo libro de trabajo con ExcelJS
    const wb = new ExcelJS.Workbook();
    const ws = wb.addWorksheet("Facturas");

    // Obtener los encabezados de la tabla
    const headers = [];
    $('#anexoVentasContribuyentes thead th').each(function() {
        headers.push($(this).text().trim());
    });

    // Agregar los encabezados como primera fila en el Excel
    ws.addRow(headers);

    // Agregar las filas de datos
    ws.addRows(data);

    // Exportar el archivo Excel
    wb.xlsx.writeBuffer().then(function(buffer) {
        const blob = new Blob([buffer], { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = "anexo_de_ventas_contribuyentes.xlsx";
        link.click();
    });

    // Cerrar el mensaje de carga
    swal.close();
}


/*=============================================
DESCARGAR TABLA VENTAS CONTRIBUYENTES EN EXCEL CSV
=============================================*/
function ventasContribuyenteCsv() {
    // Mostrar el mensaje de carga con SweetAlert
    swal({
        title: "Generando archivo",
        text: "Por favor espera mientras se genera el archivo.",
        icon: "info",
        showConfirmButton: false,
        closeOnClickOutside: false,
        closeOnEsc: false,
    });

    // Obtener la instancia de DataTable
    const dataTable = $('#anexoVentasContribuyentes').DataTable();

    // Obtener todos los datos cargados en DataTable
    const data = dataTable.rows().data().toArray();

    // Convertir los datos a formato CSV (delimitado por ";")
    let csv = "";
    data.forEach(row => {
        csv += row.join(";") + "\n"; // Filas de datos sin encabezados
    });

    // Crear un enlace para descargar el archivo CSV
    const blob = new Blob([csv], { type: "text/csv;charset=utf-8;" });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = "anexo_de_ventas_contribuyentes.csv";

    // Descargar el archivo
    link.click();

    // Cerrar el mensaje de carga
    swal.close();
}

/*=============================================
DESCARGAR TABLA COMPRAS EN EXCEL
=============================================*/
function comprasExcel() {
    // Mostrar el mensaje de carga con SweetAlert
    swal({
        title: "Generando archivo",
        text: "Por favor espera mientras se genera el archivo.",
        icon: "info",
        showConfirmButton: false,
        closeOnClickOutside: false,
        closeOnEsc: false,
    });

    // Obtener la instancia de DataTable
    const dataTable = $('#anexoCompras').DataTable();

    // Obtener todos los datos de la tabla, no solo los visibles
    const data = dataTable.rows().data().toArray();

    // Crear un nuevo libro de trabajo con ExcelJS
    const wb = new ExcelJS.Workbook();
    const ws = wb.addWorksheet("Facturas");

    // Obtener los encabezados de la tabla
    const headers = [];
    $('#anexoCompras thead th').each(function() {
        headers.push($(this).text().trim());
    });

    // Agregar los encabezados como primera fila en el Excel
    ws.addRow(headers);

    // Agregar las filas de datos
    ws.addRows(data);

    // Exportar el archivo Excel
    wb.xlsx.writeBuffer().then(function(buffer) {
        const blob = new Blob([buffer], { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = "anexo_de_compras.xlsx";
        link.click();
    });

    // Cerrar el mensaje de carga
    swal.close();
}


/*=============================================
DESCARGAR TABLA VENTAS CONTRIBUYENTES EN EXCEL CSV
=============================================*/
function comprasCsv() {
    // Mostrar el mensaje de carga con SweetAlert
    swal({
        title: "Generando archivo",
        text: "Por favor espera mientras se genera el archivo.",
        icon: "info",
        showConfirmButton: false,
        closeOnClickOutside: false,
        closeOnEsc: false,
    });

    // Obtener la instancia de DataTable
    const dataTable = $('#anexoCompras').DataTable();

    // Obtener todos los datos cargados en DataTable
    const data = dataTable.rows().data().toArray();

    // Convertir los datos a formato CSV (delimitado por ";") excluyendo la última columna
    let csv = "";
    data.forEach(row => {
        csv += row.slice(0, -1).join(";") + "\n"; // Eliminar la última columna antes de unir
    });

    // Crear un enlace para descargar el archivo CSV
    const blob = new Blob([csv], { type: "text/csv;charset=utf-8;" });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = "anexo_de_compras.csv";

    // Descargar el archivo
    link.click();

    // Cerrar el mensaje de carga
    swal.close();
}

