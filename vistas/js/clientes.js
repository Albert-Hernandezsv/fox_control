/*=============================================
DEPARTAMENTO, DISTRITO Y MUNICIPIO DE CLIENTES
=============================================*/

var distritosClientesPorDepartamento = {
	"00": [
		{ codigo: "00", nombre: "OTRO (PARA EXTRANJEROS)", municipio: "00", municipioNombre: "OTRO (PARA EXTRANJEROS)" }
	],
	"01": [
		{ codigo: "01", nombre: "AHUACHAPAN", municipio: "14", municipioNombre: "AHUACHAPAN CENTRO" },
		{ codigo: "02", nombre: "APANECA", municipio: "14", municipioNombre: "AHUACHAPAN CENTRO" },
		{ codigo: "03", nombre: "ATIQUIZAYA", municipio: "13", municipioNombre: "AHUACHAPAN NORTE" },
		{ codigo: "04", nombre: "CONCEPCION DE ATACO", municipio: "14", municipioNombre: "AHUACHAPAN CENTRO" },
		{ codigo: "05", nombre: "EL REFUGIO", municipio: "13", municipioNombre: "AHUACHAPAN NORTE" },
		{ codigo: "06", nombre: "GUAYMANGO", municipio: "15", municipioNombre: "AHUACHAPAN SUR" },
		{ codigo: "07", nombre: "JUJUTLA", municipio: "15", municipioNombre: "AHUACHAPAN SUR" },
		{ codigo: "08", nombre: "SAN FRANCISCO MENENDEZ", municipio: "15", municipioNombre: "AHUACHAPAN SUR" },
		{ codigo: "09", nombre: "SAN LORENZO", municipio: "13", municipioNombre: "AHUACHAPAN NORTE" },
		{ codigo: "10", nombre: "SAN PEDRO PUXTLA", municipio: "15", municipioNombre: "AHUACHAPAN SUR" },
		{ codigo: "11", nombre: "TACUBA", municipio: "14", municipioNombre: "AHUACHAPAN CENTRO" },
		{ codigo: "12", nombre: "TURIN", municipio: "13", municipioNombre: "AHUACHAPAN NORTE" }
	],
	"02": [
		{ codigo: "01", nombre: "CANDELARIA DE LA FRONTERA", municipio: "17", municipioNombre: "SANTA ANA OESTE" },
		{ codigo: "02", nombre: "COATEPEQUE", municipio: "16", municipioNombre: "SANTA ANA ESTE" },
		{ codigo: "03", nombre: "CHALCHUAPA", municipio: "17", municipioNombre: "SANTA ANA OESTE" },
		{ codigo: "04", nombre: "EL CONGO", municipio: "16", municipioNombre: "SANTA ANA ESTE" },
		{ codigo: "05", nombre: "EL PORVENIR", municipio: "17", municipioNombre: "SANTA ANA OESTE" },
		{ codigo: "06", nombre: "MASAHUAT", municipio: "14", municipioNombre: "SANTA ANA NORTE" },
		{ codigo: "07", nombre: "METAPAN", municipio: "14", municipioNombre: "SANTA ANA NORTE" },
		{ codigo: "08", nombre: "SAN ANTONIO PAJONAL", municipio: "17", municipioNombre: "SANTA ANA OESTE" },
		{ codigo: "09", nombre: "SAN SEBASTIAN SALITRILLO", municipio: "17", municipioNombre: "SANTA ANA OESTE" },
		{ codigo: "10", nombre: "SANTA ANA", municipio: "15", municipioNombre: "SANTA ANA CENTRO" },
		{ codigo: "11", nombre: "SANTA ROSA GUACHIPILIN", municipio: "14", municipioNombre: "SANTA ANA NORTE" },
		{ codigo: "12", nombre: "SANTIAGO DE LA FRONTERA", municipio: "17", municipioNombre: "SANTA ANA OESTE" },
		{ codigo: "13", nombre: "TEXISTEPEQUE", municipio: "14", municipioNombre: "SANTA ANA NORTE" }
	],
	"03": [
		{ codigo: "01", nombre: "ACAJUTLA", municipio: "20", municipioNombre: "SONSONATE OESTE" },
		{ codigo: "02", nombre: "ARMENIA", municipio: "19", municipioNombre: "SONSONATE ESTE" },
		{ codigo: "03", nombre: "CALUCO", municipio: "19", municipioNombre: "SONSONATE ESTE" },
		{ codigo: "04", nombre: "CUISNAHUAT", municipio: "19", municipioNombre: "SONSONATE ESTE" },
		{ codigo: "05", nombre: "SANTA ISABEL ISHUATAN", municipio: "19", municipioNombre: "SONSONATE ESTE" },
		{ codigo: "06", nombre: "IZALCO", municipio: "19", municipioNombre: "SONSONATE ESTE" },
		{ codigo: "07", nombre: "JUAYUA", municipio: "17", municipioNombre: "SONSONATE NORTE" },
		{ codigo: "08", nombre: "NAHUIZALCO", municipio: "17", municipioNombre: "SONSONATE NORTE" },
		{ codigo: "09", nombre: "NAHULINGO", municipio: "18", municipioNombre: "SONSONATE CENTRO" },
		{ codigo: "10", nombre: "SALCOATITAN", municipio: "17", municipioNombre: "SONSONATE NORTE" },
		{ codigo: "11", nombre: "SAN ANTONIO DEL MONTE", municipio: "18", municipioNombre: "SONSONATE CENTRO" },
		{ codigo: "12", nombre: "SAN JULIAN", municipio: "19", municipioNombre: "SONSONATE ESTE" },
		{ codigo: "13", nombre: "SANTA CATARINA MASAHUAT", municipio: "17", municipioNombre: "SONSONATE NORTE" },
		{ codigo: "14", nombre: "SANTO DOMINGO GUZMAN", municipio: "18", municipioNombre: "SONSONATE CENTRO" },
		{ codigo: "15", nombre: "SONSONATE", municipio: "18", municipioNombre: "SONSONATE CENTRO" },
		{ codigo: "16", nombre: "SONZACATE", municipio: "18", municipioNombre: "SONSONATE CENTRO" }
	],
	"04": [
		{ codigo: "01", nombre: "AGUA CALIENTE", municipio: "35", municipioNombre: "CHALATENANGO CENTRO" },
		{ codigo: "02", nombre: "ARCATAO", municipio: "36", municipioNombre: "CHALATENANGO SUR" },
		{ codigo: "03", nombre: "AZACUALPA", municipio: "36", municipioNombre: "CHALATENANGO SUR" },
		{ codigo: "04", nombre: "CITALA", municipio: "34", municipioNombre: "CHALATENANGO NORTE" },
		{ codigo: "05", nombre: "COMALAPA", municipio: "36", municipioNombre: "CHALATENANGO SUR" },
		{ codigo: "06", nombre: "CONCEPCION QUEZALTEPEQUE", municipio: "36", municipioNombre: "CHALATENANGO SUR" },
		{ codigo: "07", nombre: "CHALATENANGO", municipio: "36", municipioNombre: "CHALATENANGO SUR" },
		{ codigo: "08", nombre: "DULCE NOMBRE DE MARIA", municipio: "35", municipioNombre: "CHALATENANGO CENTRO" },
		{ codigo: "09", nombre: "EL CARRIZAL", municipio: "36", municipioNombre: "CHALATENANGO SUR" },
		{ codigo: "10", nombre: "EL PARAISO", municipio: "35", municipioNombre: "CHALATENANGO CENTRO" },
		{ codigo: "11", nombre: "LA LAGUNA", municipio: "36", municipioNombre: "CHALATENANGO SUR" },
		{ codigo: "12", nombre: "LA PALMA", municipio: "34", municipioNombre: "CHALATENANGO NORTE" },
		{ codigo: "13", nombre: "LA REINA", municipio: "35", municipioNombre: "CHALATENANGO CENTRO" },
		{ codigo: "14", nombre: "LAS VUELTAS", municipio: "36", municipioNombre: "CHALATENANGO SUR" },
		{ codigo: "15", nombre: "NOMBRE DE JESUS", municipio: "36", municipioNombre: "CHALATENANGO SUR" },
		{ codigo: "16", nombre: "NUEVA CONCEPCION", municipio: "35", municipioNombre: "CHALATENANGO CENTRO" },
		{ codigo: "17", nombre: "NUEVA TRINIDAD", municipio: "36", municipioNombre: "CHALATENANGO SUR" },
		{ codigo: "18", nombre: "OJOS DE AGUA", municipio: "36", municipioNombre: "CHALATENANGO SUR" },
		{ codigo: "19", nombre: "POTONICO", municipio: "36", municipioNombre: "CHALATENANGO SUR" },
		{ codigo: "20", nombre: "SAN ANTONIO DE LA CRUZ", municipio: "36", municipioNombre: "CHALATENANGO SUR" },
		{ codigo: "21", nombre: "SAN ANTONIO LOS RANCHOS", municipio: "36", municipioNombre: "CHALATENANGO SUR" },
		{ codigo: "22", nombre: "SAN FERNANDO", municipio: "35", municipioNombre: "CHALATENANGO CENTRO" },
		{ codigo: "23", nombre: "SAN FRANCISCO LEMPA", municipio: "36", municipioNombre: "CHALATENANGO SUR" },
		{ codigo: "24", nombre: "SAN FRANCISCO MORAZAN", municipio: "35", municipioNombre: "CHALATENANGO CENTRO" },
		{ codigo: "25", nombre: "SAN IGNACIO", municipio: "34", municipioNombre: "CHALATENANGO NORTE" },
		{ codigo: "26", nombre: "SAN ISIDRO LABRADOR", municipio: "36", municipioNombre: "CHALATENANGO SUR" },
		{ codigo: "27", nombre: "SAN JOSE CANCASQUE", municipio: "36", municipioNombre: "CHALATENANGO SUR" },
		{ codigo: "28", nombre: "SAN JOSE LAS FLORES", municipio: "36", municipioNombre: "CHALATENANGO SUR" },
		{ codigo: "29", nombre: "SAN LUIS DEL CARMEN", municipio: "36", municipioNombre: "CHALATENANGO SUR" },
		{ codigo: "30", nombre: "SAN MIGUEL DE MERCEDES", municipio: "36", municipioNombre: "CHALATENANGO SUR" },
		{ codigo: "31", nombre: "SAN RAFAEL", municipio: "35", municipioNombre: "CHALATENANGO CENTRO" },
		{ codigo: "32", nombre: "SANTA RITA", municipio: "35", municipioNombre: "CHALATENANGO CENTRO" },
		{ codigo: "33", nombre: "TEJUTLA", municipio: "35", municipioNombre: "CHALATENANGO CENTRO" }
	],
	"05": [
		{ codigo: "01", nombre: "ANTIGUO CUSCATLAN", municipio: "26", municipioNombre: "LA LIBERTAD ESTE" },
		{ codigo: "02", nombre: "CIUDAD ARCE", municipio: "24", municipioNombre: "LA LIBERTAD CENTRO" },
		{ codigo: "03", nombre: "COLON", municipio: "25", municipioNombre: "LA LIBERTAD OESTE" },
		{ codigo: "04", nombre: "COMASAGUA", municipio: "28", municipioNombre: "LA LIBERTAD SUR" },
		{ codigo: "05", nombre: "CHILTIUPAN", municipio: "27", municipioNombre: "LA LIBERTAD COSTA" },
		{ codigo: "06", nombre: "HUIZUCAR", municipio: "26", municipioNombre: "LA LIBERTAD ESTE" },
		{ codigo: "07", nombre: "JAYAQUE", municipio: "25", municipioNombre: "LA LIBERTAD OESTE" },
		{ codigo: "08", nombre: "JICALAPA", municipio: "27", municipioNombre: "LA LIBERTAD COSTA" },
		{ codigo: "09", nombre: "LA LIBERTAD", municipio: "27", municipioNombre: "LA LIBERTAD COSTA" },
		{ codigo: "10", nombre: "NUEVO CUSCATLAN", municipio: "26", municipioNombre: "LA LIBERTAD ESTE" },
		{ codigo: "11", nombre: "SANTA TECLA", municipio: "28", municipioNombre: "LA LIBERTAD SUR" },
		{ codigo: "12", nombre: "QUEZALTEPEQUE", municipio: "23", municipioNombre: "LA LIBERTAD NORTE" },
		{ codigo: "13", nombre: "SACACOYO", municipio: "25", municipioNombre: "LA LIBERTAD OESTE" },
		{ codigo: "14", nombre: "SAN JOSE VILLANUEVA", municipio: "26", municipioNombre: "LA LIBERTAD ESTE" },
		{ codigo: "15", nombre: "SAN JUAN OPICO", municipio: "24", municipioNombre: "LA LIBERTAD CENTRO" },
		{ codigo: "16", nombre: "SAN MATIAS", municipio: "23", municipioNombre: "LA LIBERTAD NORTE" },
		{ codigo: "17", nombre: "SAN PABLO TACACHICO", municipio: "23", municipioNombre: "LA LIBERTAD NORTE" },
		{ codigo: "18", nombre: "TAMANIQUE", municipio: "27", municipioNombre: "LA LIBERTAD COSTA" },
		{ codigo: "19", nombre: "TALNIQUE", municipio: "25", municipioNombre: "LA LIBERTAD OESTE" },
		{ codigo: "20", nombre: "TEOTEPEQUE", municipio: "27", municipioNombre: "LA LIBERTAD COSTA" },
		{ codigo: "21", nombre: "TEPECOYO", municipio: "25", municipioNombre: "LA LIBERTAD OESTE" },
		{ codigo: "22", nombre: "ZARAGOZA", municipio: "26", municipioNombre: "LA LIBERTAD ESTE" }
	],
	"06": [
		{ codigo: "01", nombre: "AGUILARES", municipio: "20", municipioNombre: "SAN SALVADOR NORTE" },
		{ codigo: "02", nombre: "APOPA", municipio: "21", municipioNombre: "SAN SALVADOR OESTE" },
		{ codigo: "03", nombre: "AYUTUXTEPEQUE", municipio: "23", municipioNombre: "SAN SALVADOR CENTRO" },
		{ codigo: "04", nombre: "CUSCATANCINGO", municipio: "22", municipioNombre: "SAN SALVADOR ESTE" },
		{ codigo: "05", nombre: "EL PAISNAL", municipio: "20", municipioNombre: "SAN SALVADOR NORTE" },
		{ codigo: "06", nombre: "GUAZAPA", municipio: "20", municipioNombre: "SAN SALVADOR NORTE" },
		{ codigo: "07", nombre: "ILOPANGO", municipio: "22", municipioNombre: "SAN SALVADOR ESTE" },
		{ codigo: "08", nombre: "MEJICANOS", municipio: "23", municipioNombre: "SAN SALVADOR CENTRO" },
		{ codigo: "09", nombre: "NEJAPA", municipio: "21", municipioNombre: "SAN SALVADOR OESTE" },
		{ codigo: "10", nombre: "PANCHIMALCO", municipio: "24", municipioNombre: "SAN SALVADOR SUR" },
		{ codigo: "11", nombre: "ROSARIO DE MORA", municipio: "24", municipioNombre: "SAN SALVADOR SUR" },
		{ codigo: "12", nombre: "SAN MARCOS", municipio: "23", municipioNombre: "SAN SALVADOR CENTRO" },
		{ codigo: "13", nombre: "SAN MARTIN", municipio: "22", municipioNombre: "SAN SALVADOR ESTE" },
		{ codigo: "14", nombre: "SAN SALVADOR", municipio: "23", municipioNombre: "SAN SALVADOR CENTRO" },
		{ codigo: "15", nombre: "SANTIAGO TEXACUANGOS", municipio: "23", municipioNombre: "SAN SALVADOR CENTRO" },
		{ codigo: "16", nombre: "SANTO TOMAS", municipio: "23", municipioNombre: "SAN SALVADOR CENTRO" },
		{ codigo: "17", nombre: "SOYAPANGO", municipio: "22", municipioNombre: "SAN SALVADOR ESTE" },
		{ codigo: "18", nombre: "TONACATEPEQUE", municipio: "22", municipioNombre: "SAN SALVADOR ESTE" },
		{ codigo: "19", nombre: "CIUDAD DELGADO", municipio: "22", municipioNombre: "SAN SALVADOR ESTE" }
	],
	"07": [
		{ codigo: "01", nombre: "CANDELARIA", municipio: "18", municipioNombre: "CUSCATLAN SUR" },
		{ codigo: "02", nombre: "COJUTEPEQUE", municipio: "18", municipioNombre: "CUSCATLAN SUR" },
		{ codigo: "03", nombre: "EL CARMEN", municipio: "18", municipioNombre: "CUSCATLAN SUR" },
		{ codigo: "04", nombre: "EL ROSARIO", municipio: "18", municipioNombre: "CUSCATLAN SUR" },
		{ codigo: "05", nombre: "MONTE SAN JUAN", municipio: "18", municipioNombre: "CUSCATLAN SUR" },
		{ codigo: "06", nombre: "ORATORIO DE CONCEPCION", municipio: "17", municipioNombre: "CUSCATLAN NORTE" },
		{ codigo: "07", nombre: "SAN BARTOLOME PERULAPIA", municipio: "17", municipioNombre: "CUSCATLAN NORTE" },
		{ codigo: "08", nombre: "SAN CRISTOBAL", municipio: "18", municipioNombre: "CUSCATLAN SUR" },
		{ codigo: "09", nombre: "SAN JOSE GUAYABAL", municipio: "17", municipioNombre: "CUSCATLAN NORTE" },
		{ codigo: "10", nombre: "SAN PEDRO PERULAPAN", municipio: "17", municipioNombre: "CUSCATLAN NORTE" },
		{ codigo: "11", nombre: "SAN RAFAEL CEDROS", municipio: "18", municipioNombre: "CUSCATLAN SUR" },
		{ codigo: "12", nombre: "SAN RAMON", municipio: "18", municipioNombre: "CUSCATLAN SUR" },
		{ codigo: "13", nombre: "SANTA CRUZ ANALQUITO", municipio: "18", municipioNombre: "CUSCATLAN SUR" },
		{ codigo: "14", nombre: "SANTA CRUZ MICHAPA", municipio: "18", municipioNombre: "CUSCATLAN SUR" },
		{ codigo: "15", nombre: "SUCHITOTO", municipio: "17", municipioNombre: "CUSCATLAN NORTE" },
		{ codigo: "16", nombre: "TENANCINGO", municipio: "18", municipioNombre: "CUSCATLAN SUR" }
	],
	"08": [
		{ codigo: "01", nombre: "CUYULTITAN", municipio: "23", municipioNombre: "LA PAZ OESTE" },
		{ codigo: "02", nombre: "EL ROSARIO", municipio: "24", municipioNombre: "LA PAZ CENTRO" },
		{ codigo: "03", nombre: "JERUSALEN", municipio: "24", municipioNombre: "LA PAZ CENTRO" },
		{ codigo: "04", nombre: "MERCEDES LA CEIBA", municipio: "24", municipioNombre: "LA PAZ CENTRO" },
		{ codigo: "05", nombre: "OLOCUILTA", municipio: "23", municipioNombre: "LA PAZ OESTE" },
		{ codigo: "06", nombre: "PARAISO DE OSORIO", municipio: "24", municipioNombre: "LA PAZ CENTRO" },
		{ codigo: "07", nombre: "SAN ANTONIO MASAHUAT", municipio: "24", municipioNombre: "LA PAZ CENTRO" },
		{ codigo: "08", nombre: "SAN EMIGDIO", municipio: "24", municipioNombre: "LA PAZ CENTRO" },
		{ codigo: "09", nombre: "SAN FRANCISCO CHINAMECA", municipio: "23", municipioNombre: "LA PAZ OESTE" },
		{ codigo: "10", nombre: "SAN JUAN NONUALCO", municipio: "25", municipioNombre: "LA PAZ ESTE" },
		{ codigo: "11", nombre: "SAN JUAN TALPA", municipio: "23", municipioNombre: "LA PAZ OESTE" },
		{ codigo: "12", nombre: "SAN JUAN TEPEZONTES", municipio: "24", municipioNombre: "LA PAZ CENTRO" },
		{ codigo: "13", nombre: "SAN LUIS TALPA", municipio: "23", municipioNombre: "LA PAZ OESTE" },
		{ codigo: "14", nombre: "SAN MIGUEL TEPEZONTES", municipio: "24", municipioNombre: "LA PAZ CENTRO" },
		{ codigo: "15", nombre: "SAN PEDRO MASAHUAT", municipio: "23", municipioNombre: "LA PAZ OESTE" },
		{ codigo: "16", nombre: "SAN PEDRO NONUALCO", municipio: "24", municipioNombre: "LA PAZ CENTRO" },
		{ codigo: "17", nombre: "SAN RAFAEL OBRAJUELO", municipio: "25", municipioNombre: "LA PAZ ESTE" },
		{ codigo: "18", nombre: "SANTA MARIA OSTUMA", municipio: "24", municipioNombre: "LA PAZ CENTRO" },
		{ codigo: "19", nombre: "SANTIAGO NONUALCO", municipio: "24", municipioNombre: "LA PAZ CENTRO" },
		{ codigo: "20", nombre: "TAPALHUACA", municipio: "23", municipioNombre: "LA PAZ OESTE" },
		{ codigo: "21", nombre: "ZACATECOLUCA", municipio: "25", municipioNombre: "LA PAZ ESTE" },
		{ codigo: "22", nombre: "SAN LUIS LA HERRADURA", municipio: "24", municipioNombre: "LA PAZ CENTRO" }
	],
	"09": [
		{ codigo: "01", nombre: "CINQUERA", municipio: "10", municipioNombre: "CABANAS OESTE" },
		{ codigo: "02", nombre: "GUACOTECTI", municipio: "11", municipioNombre: "CABANAS ESTE" },
		{ codigo: "03", nombre: "ILOBASCO", municipio: "10", municipioNombre: "CABANAS OESTE" },
		{ codigo: "04", nombre: "JUTIAPA", municipio: "10", municipioNombre: "CABANAS OESTE" },
		{ codigo: "05", nombre: "SAN ISIDRO", municipio: "11", municipioNombre: "CABANAS ESTE" },
		{ codigo: "06", nombre: "SENSUNTEPEQUE", municipio: "11", municipioNombre: "CABANAS ESTE" },
		{ codigo: "07", nombre: "TEJUTEPEQUE", municipio: "10", municipioNombre: "CABANAS OESTE" },
		{ codigo: "08", nombre: "VICTORIA", municipio: "11", municipioNombre: "CABANAS ESTE" },
		{ codigo: "09", nombre: "DOLORES", municipio: "11", municipioNombre: "CABANAS ESTE" }
	],
	"10": [
		{ codigo: "01", nombre: "APASTEPEQUE", municipio: "14", municipioNombre: "SAN VICENTE NORTE" },
		{ codigo: "02", nombre: "GUADALUPE", municipio: "15", municipioNombre: "SAN VICENTE SUR" },
		{ codigo: "03", nombre: "SAN CAYETANO ISTEPEQUE", municipio: "15", municipioNombre: "SAN VICENTE SUR" },
		{ codigo: "04", nombre: "SANTA CLARA", municipio: "14", municipioNombre: "SAN VICENTE NORTE" },
		{ codigo: "05", nombre: "SANTO DOMINGO", municipio: "14", municipioNombre: "SAN VICENTE NORTE" },
		{ codigo: "06", nombre: "SAN ESTEBAN CATARINA", municipio: "14", municipioNombre: "SAN VICENTE NORTE" },
		{ codigo: "07", nombre: "SAN ILDEFONSO", municipio: "14", municipioNombre: "SAN VICENTE NORTE" },
		{ codigo: "08", nombre: "SAN LORENZO", municipio: "14", municipioNombre: "SAN VICENTE NORTE" },
		{ codigo: "09", nombre: "SAN SEBASTIAN", municipio: "14", municipioNombre: "SAN VICENTE NORTE" },
		{ codigo: "10", nombre: "SAN VICENTE", municipio: "15", municipioNombre: "SAN VICENTE SUR" },
		{ codigo: "11", nombre: "TECOLUCA", municipio: "15", municipioNombre: "SAN VICENTE SUR" },
		{ codigo: "12", nombre: "TEPETITAN", municipio: "15", municipioNombre: "SAN VICENTE SUR" },
		{ codigo: "13", nombre: "VERAPAZ", municipio: "15", municipioNombre: "SAN VICENTE SUR" }
	],
	"11": [
		{ codigo: "01", nombre: "ALEGRIA", municipio: "24", municipioNombre: "USULUTAN NORTE" },
		{ codigo: "02", nombre: "BERLIN", municipio: "24", municipioNombre: "USULUTAN NORTE" },
		{ codigo: "03", nombre: "CALIFORNIA", municipio: "25", municipioNombre: "USULUTAN ESTE" },
		{ codigo: "04", nombre: "CONCEPCION BATRES", municipio: "25", municipioNombre: "USULUTAN ESTE" },
		{ codigo: "05", nombre: "EL TRIUNFO", municipio: "24", municipioNombre: "USULUTAN NORTE" },
		{ codigo: "06", nombre: "EREGUAYQUIN", municipio: "25", municipioNombre: "USULUTAN ESTE" },
		{ codigo: "07", nombre: "ESTANZUELAS", municipio: "24", municipioNombre: "USULUTAN NORTE" },
		{ codigo: "08", nombre: "JIQUILISCO", municipio: "26", municipioNombre: "USULUTAN OESTE" },
		{ codigo: "09", nombre: "JUCUAPA", municipio: "24", municipioNombre: "USULUTAN NORTE" },
		{ codigo: "10", nombre: "JUCUARAN", municipio: "25", municipioNombre: "USULUTAN ESTE" },
		{ codigo: "11", nombre: "MERCEDES UMANA", municipio: "24", municipioNombre: "USULUTAN NORTE" },
		{ codigo: "12", nombre: "NUEVA GRANADA", municipio: "24", municipioNombre: "USULUTAN NORTE" },
		{ codigo: "13", nombre: "OZATLAN", municipio: "25", municipioNombre: "USULUTAN ESTE" },
		{ codigo: "14", nombre: "PUERTO EL TRIUNFO", municipio: "26", municipioNombre: "USULUTAN OESTE" },
		{ codigo: "15", nombre: "SAN AGUSTIN", municipio: "26", municipioNombre: "USULUTAN OESTE" },
		{ codigo: "16", nombre: "SAN BUENAVENTURA", municipio: "24", municipioNombre: "USULUTAN NORTE" },
		{ codigo: "17", nombre: "SAN DIONISIO", municipio: "25", municipioNombre: "USULUTAN ESTE" },
		{ codigo: "18", nombre: "SANTA ELENA", municipio: "25", municipioNombre: "USULUTAN ESTE" },
		{ codigo: "19", nombre: "SAN FRANCISCO JAVIER", municipio: "26", municipioNombre: "USULUTAN OESTE" },
		{ codigo: "20", nombre: "SANTA MARIA", municipio: "25", municipioNombre: "USULUTAN ESTE" },
		{ codigo: "21", nombre: "SANTIAGO DE MARIA", municipio: "24", municipioNombre: "USULUTAN NORTE" },
		{ codigo: "22", nombre: "TECAPAN", municipio: "25", municipioNombre: "USULUTAN ESTE" },
		{ codigo: "23", nombre: "USULUTAN", municipio: "25", municipioNombre: "USULUTAN ESTE" }
	],
	"12": [
		{ codigo: "01", nombre: "CAROLINA", municipio: "21", municipioNombre: "SAN MIGUEL NORTE" },
		{ codigo: "02", nombre: "CIUDAD BARRIOS", municipio: "21", municipioNombre: "SAN MIGUEL NORTE" },
		{ codigo: "03", nombre: "COMACARAN", municipio: "22", municipioNombre: "SAN MIGUEL CENTRO" },
		{ codigo: "04", nombre: "CHAPELTIQUE", municipio: "21", municipioNombre: "SAN MIGUEL NORTE" },
		{ codigo: "05", nombre: "CHINAMECA", municipio: "23", municipioNombre: "SAN MIGUEL OESTE" },
		{ codigo: "06", nombre: "CHIRILAGUA", municipio: "22", municipioNombre: "SAN MIGUEL CENTRO" },
		{ codigo: "07", nombre: "EL TRANSITO", municipio: "23", municipioNombre: "SAN MIGUEL OESTE" },
		{ codigo: "08", nombre: "LOLOTIQUE", municipio: "23", municipioNombre: "SAN MIGUEL OESTE" },
		{ codigo: "09", nombre: "MONCAGUA", municipio: "22", municipioNombre: "SAN MIGUEL CENTRO" },
		{ codigo: "10", nombre: "NUEVA GUADALUPE", municipio: "23", municipioNombre: "SAN MIGUEL OESTE" },
		{ codigo: "11", nombre: "NUEVO EDEN DE SAN JUAN", municipio: "21", municipioNombre: "SAN MIGUEL NORTE" },
		{ codigo: "12", nombre: "QUELEPA", municipio: "22", municipioNombre: "SAN MIGUEL CENTRO" },
		{ codigo: "13", nombre: "SAN ANTONIO DEL MOSCO", municipio: "21", municipioNombre: "SAN MIGUEL NORTE" },
		{ codigo: "14", nombre: "SAN GERARDO", municipio: "21", municipioNombre: "SAN MIGUEL NORTE" },
		{ codigo: "15", nombre: "SAN JORGE", municipio: "23", municipioNombre: "SAN MIGUEL OESTE" },
		{ codigo: "16", nombre: "SAN LUIS DE LA REINA", municipio: "21", municipioNombre: "SAN MIGUEL NORTE" },
		{ codigo: "17", nombre: "SAN MIGUEL", municipio: "22", municipioNombre: "SAN MIGUEL CENTRO" },
		{ codigo: "18", nombre: "SAN RAFAEL ORIENTE", municipio: "23", municipioNombre: "SAN MIGUEL OESTE" },
		{ codigo: "19", nombre: "SESORI", municipio: "21", municipioNombre: "SAN MIGUEL NORTE" },
		{ codigo: "20", nombre: "ULUAZAPA", municipio: "22", municipioNombre: "SAN MIGUEL CENTRO" }
	],
	"13": [
		{ codigo: "01", nombre: "ARAMBALA", municipio: "27", municipioNombre: "MORAZAN NORTE" },
		{ codigo: "02", nombre: "CACAOPERA", municipio: "27", municipioNombre: "MORAZAN NORTE" },
		{ codigo: "03", nombre: "CORINTO", municipio: "27", municipioNombre: "MORAZAN NORTE" },
		{ codigo: "04", nombre: "CHILANGA", municipio: "28", municipioNombre: "MORAZAN SUR" },
		{ codigo: "05", nombre: "DELICIAS DE CONCEPCION", municipio: "28", municipioNombre: "MORAZAN SUR" },
		{ codigo: "06", nombre: "EL DIVISADERO", municipio: "28", municipioNombre: "MORAZAN SUR" },
		{ codigo: "07", nombre: "EL ROSARIO", municipio: "27", municipioNombre: "MORAZAN NORTE" },
		{ codigo: "08", nombre: "GUALOCOCTI", municipio: "28", municipioNombre: "MORAZAN SUR" },
		{ codigo: "09", nombre: "GUATAJIAGUA", municipio: "28", municipioNombre: "MORAZAN SUR" },
		{ codigo: "10", nombre: "JOATECA", municipio: "27", municipioNombre: "MORAZAN NORTE" },
		{ codigo: "11", nombre: "JOCOAITIQUE", municipio: "27", municipioNombre: "MORAZAN NORTE" },
		{ codigo: "12", nombre: "JOCORO", municipio: "28", municipioNombre: "MORAZAN SUR" },
		{ codigo: "13", nombre: "LOLOTIQUILLO", municipio: "28", municipioNombre: "MORAZAN SUR" },
		{ codigo: "14", nombre: "MEANGUERA", municipio: "27", municipioNombre: "MORAZAN NORTE" },
		{ codigo: "15", nombre: "OSICALA", municipio: "28", municipioNombre: "MORAZAN SUR" },
		{ codigo: "16", nombre: "PERQUIN", municipio: "27", municipioNombre: "MORAZAN NORTE" },
		{ codigo: "17", nombre: "SAN CARLOS", municipio: "28", municipioNombre: "MORAZAN SUR" },
		{ codigo: "18", nombre: "SAN FERNANDO", municipio: "27", municipioNombre: "MORAZAN NORTE" },
		{ codigo: "19", nombre: "SAN FRANCISCO GOTERA", municipio: "28", municipioNombre: "MORAZAN SUR" },
		{ codigo: "20", nombre: "SAN ISIDRO", municipio: "27", municipioNombre: "MORAZAN NORTE" },
		{ codigo: "21", nombre: "SAN SIMON", municipio: "28", municipioNombre: "MORAZAN SUR" },
		{ codigo: "22", nombre: "SENSEMBRA", municipio: "28", municipioNombre: "MORAZAN SUR" },
		{ codigo: "23", nombre: "SOCIEDAD", municipio: "28", municipioNombre: "MORAZAN SUR" },
		{ codigo: "24", nombre: "TOROLA", municipio: "27", municipioNombre: "MORAZAN NORTE" },
		{ codigo: "25", nombre: "YAMABAL", municipio: "28", municipioNombre: "MORAZAN SUR" },
		{ codigo: "26", nombre: "YOLOAIQUIN", municipio: "28", municipioNombre: "MORAZAN SUR" }
	],
	"14": [
		{ codigo: "01", nombre: "ANAMOROS", municipio: "19", municipioNombre: "LA UNION NORTE" },
		{ codigo: "02", nombre: "BOLIVAR", municipio: "19", municipioNombre: "LA UNION NORTE" },
		{ codigo: "03", nombre: "CONCEPCION DE ORIENTE", municipio: "19", municipioNombre: "LA UNION NORTE" },
		{ codigo: "04", nombre: "CONCHAGUA", municipio: "20", municipioNombre: "LA UNION SUR" },
		{ codigo: "05", nombre: "EL CARMEN", municipio: "20", municipioNombre: "LA UNION SUR" },
		{ codigo: "06", nombre: "EL SAUCE", municipio: "19", municipioNombre: "LA UNION NORTE" },
		{ codigo: "07", nombre: "INTIPUCA", municipio: "20", municipioNombre: "LA UNION SUR" },
		{ codigo: "08", nombre: "LA UNION", municipio: "20", municipioNombre: "LA UNION SUR" },
		{ codigo: "09", nombre: "LISLIQUE", municipio: "19", municipioNombre: "LA UNION NORTE" },
		{ codigo: "10", nombre: "MEANGUERA DEL GOLFO", municipio: "20", municipioNombre: "LA UNION SUR" },
		{ codigo: "11", nombre: "NUEVA ESPARTA", municipio: "19", municipioNombre: "LA UNION NORTE" },
		{ codigo: "12", nombre: "PASAQUINA", municipio: "19", municipioNombre: "LA UNION NORTE" },
		{ codigo: "13", nombre: "POLOROS", municipio: "19", municipioNombre: "LA UNION NORTE" },
		{ codigo: "14", nombre: "SAN ALEJO", municipio: "20", municipioNombre: "LA UNION SUR" },
		{ codigo: "15", nombre: "SAN JOSE", municipio: "19", municipioNombre: "LA UNION NORTE" },
		{ codigo: "16", nombre: "SANTA ROSA DE LIMA", municipio: "19", municipioNombre: "LA UNION NORTE" },
		{ codigo: "17", nombre: "YAYANTIQUE", municipio: "20", municipioNombre: "LA UNION SUR" },
		{ codigo: "18", nombre: "YUCUAIQUIN", municipio: "20", municipioNombre: "LA UNION SUR" }
	]
};

function prefijoUbicacionPorSelect($select) {
	return ($select.attr("id") || "").indexOf("editar") === 0 ? "editar" : "nuevo";
}

function sufijoUbicacionPorSelect($select) {
	return ($select.attr("id") || "").indexOf("Empresa") !== -1 ? "Empresa" : "Cliente";
}

function selectsUbicacion(prefijo, sufijo) {
	return {
		departamento: $("#" + prefijo + "Departamento" + sufijo),
		distrito: $("#" + prefijo + "Distrito" + sufijo),
		municipio: $("#" + prefijo + "Municipio" + sufijo)
	};
}

function limpiarMunicipioCliente($municipio, texto) {
	$municipio.html('<option value="" selected>' + (texto || "Se completara automaticamente") + '</option>');
	$municipio.val("");
}

function cargarDistritosUbicacion(prefijo, sufijo, departamento, distritoSeleccionado) {
	var selects = selectsUbicacion(prefijo, sufijo);
	var distritos = distritosClientesPorDepartamento[departamento] || [];

	selects.distrito.empty();
	limpiarMunicipioCliente(selects.municipio);

	if (distritos.length === 0) {
		selects.distrito.append('<option value="" selected>Seleccione primero un departamento</option>');
		return;
	}

	selects.distrito.append('<option value="" selected>Seleccione una opcion</option>');

	distritos.forEach(function(distrito) {
		var opcion = $("<option></option>")
			.val(distrito.codigo)
			.text(distrito.codigo + " - " + distrito.nombre)
			.attr("data-municipio", distrito.municipio)
			.attr("data-municipio-nombre", distrito.municipioNombre);

		if (distrito.codigo === distritoSeleccionado) {
			opcion.prop("selected", true);
		}

		selects.distrito.append(opcion);
	});

	if (distritoSeleccionado) {
		colocarMunicipioUbicacion(prefijo, sufijo);
	}
}

function colocarMunicipioUbicacion(prefijo, sufijo) {
	var selects = selectsUbicacion(prefijo, sufijo);
	var opcion = selects.distrito.find("option:selected");
	var municipio = opcion.attr("data-municipio") || "";
	var municipioNombre = opcion.attr("data-municipio-nombre") || "";

	if (municipio === "") {
		limpiarMunicipioCliente(selects.municipio);
		return;
	}

	selects.municipio.html('<option value="' + municipio + '" selected>' + municipio + " - " + municipioNombre + '</option>');
	selects.municipio.val(municipio);
}

function cargarDistritosCliente(prefijo, departamento, distritoSeleccionado) {
	cargarDistritosUbicacion(prefijo, "Cliente", departamento, distritoSeleccionado);
}

function colocarMunicipioCliente(prefijo) {
	colocarMunicipioUbicacion(prefijo, "Cliente");
}

function cargarDistritosEmpresa(departamento, distritoSeleccionado) {
	cargarDistritosUbicacion("editar", "Empresa", departamento, distritoSeleccionado);
}

$(document).on("change", ".selectDepartamentoCliente, .selectDepartamentoEmpresa", function() {
	var $select = $(this);
	cargarDistritosUbicacion(prefijoUbicacionPorSelect($select), sufijoUbicacionPorSelect($select), $select.val(), "");
});

$(document).on("change", ".selectDistritoCliente, .selectDistritoEmpresa", function() {
	var $select = $(this);
	colocarMunicipioUbicacion(prefijoUbicacionPorSelect($select), sufijoUbicacionPorSelect($select));
});

$(document).ready(function() {
	if ($("#nuevoDistritoCliente").length) {
		cargarDistritosCliente("nuevo", $("#nuevoDepartamentoCliente").val(), "");
	}

	if ($("#editarDistritoCliente").length) {
		cargarDistritosCliente("editar", $("#editarDepartamentoCliente").val(), "");
	}

	if ($("#editarDistritoEmpresa").length) {
		cargarDistritosEmpresa($("#editarDepartamentoEmpresa").val(), "");
	}
});

$("#modalCrearCliente").on("shown.bs.modal", function() {
	if (!$("#nuevoDepartamentoCliente").val()) {
		cargarDistritosCliente("nuevo", "", "");
	}
});

/*=============================================
EDITAR CLIENTE
=============================================*/

$(".tablas").on("click", ".btnEditarCliente", function(){
	console.log("aqui");
	var idCliente = $(this).attr("idCliente");
	
	var datos = new FormData();
	datos.append("idCliente", idCliente);

	$.ajax({

		url:"ajax/clientes.ajax.php",
		method: "POST",
		data: datos,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function(respuesta){
			
			$("#editarIdCliente").val(respuesta["id"]);
			$("#editarNombreCliente").val(respuesta["nombre"]);
			$("#editarDireccionCliente").val(respuesta["direccion"]);
			$("#editarCorreoCliente").val(respuesta["correo"]);
			$("#editarNITCliente").val(respuesta["NIT"]);
			$("#editarDUICliente").val(respuesta["DUI"]);
			$("#editarNRCCliente").val(respuesta["NRC"]);
			$("#editarDepartamentoCliente").val(respuesta["departamento"]);
			cargarDistritosCliente("editar", respuesta["departamento"], respuesta["distrito"]);
			if (!respuesta["distrito"]) {
				$("#editarMunicipioCliente").html('<option value="' + respuesta["municipio"] + '" selected>' + respuesta["municipio"] + '</option>');
			}
			$("#editarNumeroCliente").val(respuesta["telefono"]);
			$("#editarContribu").val(respuesta["tipo_cliente"]);
			$("#editarContribu").html(respuesta["tipo_cliente"]);
			$("#editarCodActividad").val(respuesta["codActividad"]);
			$("#editarDescActividad").val(respuesta["descActividad"]);
			$("#editarPaisRecibir").val(respuesta["codPais"]);
			$("#editarPaisRecibir").html(respuesta["nombrePais"]);
			$("#editarTipoPersona").val(respuesta["tipoPersona"]);
			$("#editarTipoPersona").html(respuesta["tipoPersona"]);
		}

	});

})

/*=============================================
EDITAR MOTORISTA
=============================================*/

$(".tablas").on("click", ".btnEditarMotorista", function(){
	console.log("aqui");
	var idMotorista = $(this).attr("idMotorista");
	
	var datos = new FormData();
	datos.append("idMotorista", idMotorista);

	$.ajax({

		url:"ajax/clientes.ajax.php",
		method: "POST",
		data: datos,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function(respuesta){
			
			$("#editarIdMotorista").val(respuesta["id"]);
			$("#editarNombreMotorista").val(respuesta["nombre"]);
			$("#editarDuiMotorista").val(respuesta["duiMotorista"]);
			$("#editarPlacaMotorista").val(respuesta["placaMotorista"]);
		}

	});

})

/*=============================================
EDITAR PROVEEDOR
=============================================*/

$(".tablas").on("click", ".btnEditarProveedor", function(){
	console.log("aqui");
	var idProveedor = $(this).attr("idProveedor");
	
	var datos = new FormData();
	datos.append("idProveedor", idProveedor);

	$.ajax({

		url:"ajax/clientes.ajax.php",
		method: "POST",
		data: datos,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function(respuesta){
			
			$("#editarIdProveedor").val(respuesta["id"]);
			$("#editarNombreProveedor").val(respuesta["nombre"]);
			$("#editarNitProveedor").val(respuesta["nit"]);
			$("#editarNumeroProveedor").val(respuesta["telefono"]);
			$("#editarCorreoProveedor").val(respuesta["correo"]);
			$("#editarDireccionProveedor").val(respuesta["direccion"]);
			$("#editarCondicionProveedor").val(respuesta["condicion_pago"]);
		}

	});

})

/*=============================================
EDITAR COMPRA
=============================================*/

$(".tablas").on("click", ".btnEditarCompra", function(){
		
	var idCompra = $(this).attr("idCompra");
	
	var datos = new FormData();
	datos.append("idCompra", idCompra);

	$.ajax({

		url:"ajax/clientes.ajax.php",
		method: "POST",
		data: datos,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function(respuesta){
			console.log(respuesta);		
			$("#editarIdCompra").val(respuesta["id"]);
			$("#editarFechaCompra").val(respuesta["fecha"]);
			$("#editarclase_documentoComprat").val(respuesta["clase_documento"]);
			$("#editarclase_documentoComprat").html(respuesta["clase_documento"]);
			$("#editartipo_documentoComprat").val(respuesta["tipo_documento"]);
			$("#editartipo_documentoComprat").html(respuesta["tipo_documento"]);
			$("#editarnumero_documentoCompra").val(respuesta["numero_documento"]);
			$("#editarnit_nrcCompra").val(respuesta["nit_nrc"]);
			$("#editarnombre_proveedorCompra").val(respuesta["nombre_proveedor"]);
			$("#editarcompras_internas_exentasCompra").val(respuesta["compras_internas_exentas"]);
			$("#editarinternaciones_exentas_y_no_sujetasCompra").val(respuesta["internaciones_exentas_y_no_sujetas"]);
			$("#editarimportaciones_exentas_y_no_sujetasCompra").val(respuesta["importaciones_exentas_y_no_sujetas"]);
			$("#editarcompras_internas_gravadasCompra").val(respuesta["compras_internas_gravadas"]);
			$("#editarinternaciones_gravadas_de_bienesCompra").val(respuesta["internaciones_gravadas_de_bienes"]);
			$("#editarimportaciones_gravadas_de_bienesCompra").val(respuesta["importaciones_gravadas_de_bienes"]);
			$("#editarimportaciones_gravadas_de_serviciosCompra").val(respuesta["importaciones_gravadas_de_servicios"]);
			$("#editarcredito_fiscalCompra").val(respuesta["credito_fiscal"]);
			$("#editartotal_de_comprasCompra").val(respuesta["total_de_compras"]);
			$("#editardui_del_proveedorCompra").val(respuesta["dui_del_proveedor"]);
			$("#editartipo_de_operacionComprat").val(respuesta["tipo_de_operacion"]);
			$("#editartipo_de_operacionComprat").html(respuesta["tipo_de_operacion"]);
			$("#editarclasificacionComprat").val(respuesta["clasificacion"]);
			$("#editarclasificacionComprat").html(respuesta["clasificacion"]);
			$("#editarsectorComprat").val(respuesta["sector"]);
			$("#editarsectorComprat").html(respuesta["sector"]);
			$("#editartipoComprat").val(respuesta["tipo"]);
			$("#editartipoComprat").html(respuesta["tipo"]);
			$("#editaranexoCompra").val(respuesta["anexo"]);		}

	});

})

/*=============================================
ELIMINAR CCOMPRA
=============================================*/
$(".tablas").on("click", ".btnEliminarCompra", function(){

    var idCompra = $(this).attr("idCompra");

	swal({
		title: '¿Está seguro de borrar la compra?',
		text: "¡Si no lo está puede cancelar la accíón!",
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			cancelButtonText: 'Cancelar',
			confirmButtonText: 'Si, borrar compra!'
		}).then(function(result){
	
		if(result.value){
	
			window.location = "index.php?ruta=ver-compras&idCompraEliminar="+idCompra;
	
		}
	
		})

});

/*=============================================
ELIMINAR CLIENTE
=============================================*/
$(".tablas").on("click", ".btnEliminarCliente", function(){

    var idCliente = $(this).attr("idCliente");
	var idClienteValidar = $(this).attr("idCliente");

	var datos = new FormData();
	datos.append("idClienteValidar", idClienteValidar);

	$.ajax({

		url:"ajax/clientes.ajax.php",
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
					title: 'No puedes eliminar un cliente que tenga facturas',
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
					title: '¿Está seguro de borrar el cliente?',
					text: "¡Si no lo está puede cancelar la accíón!",
					type: 'warning',
					showCancelButton: true,
					confirmButtonColor: '#3085d6',
						cancelButtonColor: '#d33',
						cancelButtonText: 'Cancelar',
						confirmButtonText: 'Si, borrar cliente!'
					}).then(function(result){
				
					if(result.value){
				
						window.location = "index.php?ruta=facturacion&idClienteEliminar="+idCliente;
				
					}
				
					})
			
			}
		}

	});

});

/*=============================================
ELIMINAR MOTORISTA
=============================================*/
$(".tablas").on("click", ".btnEliminarMotorista", function(){
	console.log("as");
    var idMotorista = $(this).attr("idMotorista");
	var idMotoristaValidar = $(this).attr("idMotorista");

	var datos = new FormData();
	datos.append("idMotoristaValidar", idMotoristaValidar);

	$.ajax({

		url:"ajax/clientes.ajax.php",
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
					title: 'No puedes eliminar un motorista que tenga facturas',
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
					title: '¿Está seguro de borrar el motorista?',
					text: "¡Si no lo está puede cancelar la accíón!",
					type: 'warning',
					showCancelButton: true,
					confirmButtonColor: '#3085d6',
						cancelButtonColor: '#d33',
						cancelButtonText: 'Cancelar',
						confirmButtonText: 'Si, borrar motorista!'
					}).then(function(result){
				
					if(result.value){
				
						window.location = "index.php?ruta=facturacion&idMotoristaEliminar="+idMotorista;
				
					}
				
					})
			
			}
		}

	});

});

/*=============================================
ELIMINAR PROVEEDOR
=============================================*/
$(".tablas").on("click", ".btnEliminarProveedor", function(){

    var idProveedor = $(this).attr("idProveedor");
	
	swal({
		title: '¿Está seguro de borrar el proveedor?',
		text: "¡Si no lo está puede cancelar la accíón!",
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			cancelButtonText: 'Cancelar',
			confirmButtonText: 'Si, borrar proveedor!'
		}).then(function(result){
	
			if(result.value){
		
				window.location = "index.php?ruta=inventario&idProveedorEliminar="+idProveedor;
		
			}
		});

});

/*=============================================
EDITAR DATOS EMPRESARIALES
=============================================*/

$('.btnEditarEmpresarial').on('click', function(e) {
	
	var idEmpresa = 1;
	
	var datos = new FormData();
	datos.append("idEmpresa", idEmpresa);

	$.ajax({

		url:"ajax/clientes.ajax.php",
		method: "POST",
		data: datos,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function(respuesta){

			$("#editarNITEmpresa").val(respuesta["nit"]);
			$("#editarNRCEmpresa").val(respuesta["nrc"]);
			$("#editarPasswordPriEmpresa").val(respuesta["passwordPri"]);
			$("#editarContraDescuento").val(respuesta["contra_descuentos"]);
			$("#editarNombreEmpresa").val(respuesta["nombre"]);
			$("#editarCodigoActividadEmpresa").val(respuesta["codActividad"]);
			$("#editarActividadEmpresa").val(respuesta["desActividad"]);
			$("#editarEstablecimientoEmpresa").val(respuesta["tipoEstablecimiento"]);
			$("#editarTipoPdfEmpresa").val(respuesta["tipo_pdf"] || "00");
			$("#editarDepartamentoEmpresa").val(respuesta["departamento"]);
			cargarDistritosEmpresa(respuesta["departamento"], respuesta["distrito"]);
			if (!respuesta["distrito"]) {
				$("#editarMunicipioEmpresa").html('<option value="' + respuesta["municipio"] + '" selected>' + respuesta["municipio"] + '</option>');
			}
			$("#editarDireccionEmpresa").val(respuesta["direccion"]);
			$("#editarNumeroEmpresa").val(respuesta["telefono"]);
			$("#editarCorreoEmpresa").val(respuesta["correo"]);
		}

	});

})

/*=============================================
EDITAR TICKET
=============================================*/

$('.editarTicket').on('click', function(e) {
	
	var idEmpresa = 1;
	
	var datos = new FormData();
	datos.append("idEmpresa", idEmpresa);

	$.ajax({

		url:"ajax/clientes.ajax.php",
		method: "POST",
		data: datos,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function(respuesta){

			$("#editarAnchoTicket").val(respuesta["ancho"]);			
		}

	});

})

/*=============================================
NCR NECESARIO PARA LOS 3 TIPOS DE CLIENTES QUE LO NECESITAN AL CREAR
=============================================*/

 // Detecta cambios en el campo tipo de contribuyente
 $('#nuevoTipoContribuyentes').change(function() {
	// Si el valor seleccionado no es "00", agrega required al campo NRC
	if ($(this).val() !== "00") {
		$('#nuevoNRCCliente').attr('required', 'required');
		$('#nuevoDUICliente').removeAttr('required');
	} else {
		// De lo contrario, remueve el atributo required
		$('#nuevoNRCCliente').removeAttr('required');
		$('#nuevoDUICliente').attr('required', 'required');
	}
});

/*=============================================
NCR NECESARIO PARA LOS 3 TIPOS DE CLIENTES QUE LO NECESITAN AL EDITAR
=============================================*/

 // Detecta cambios en el campo tipo de contribuyente
 $('#editarTipoContribuyentes').change(function() {
	// Si el valor seleccionado no es "00", agrega required al campo NRC
	if ($(this).val() !== "00") {
		$('#editarNRCCliente').attr('required', 'required');
	} else {
		// De lo contrario, remueve el atributo required
		$('#editarNRCCliente').removeAttr('required');
	}
});

/*=============================================
ENVIAR A ESCOGER FACTURA
=============================================*/

$(".tablas").on("click", ".btnEscogerFactura", function() {
	
	var idCliente = $(this).attr("idCliente");
	
	window.location = "index.php?ruta=escoger-factura&idClienteEscogerFactura="+idCliente;

})

/*=============================================
ENVIAR A ESCOGER FACTURA CONTINGENCIA
=============================================*/

$(".tablas").on("click", ".btnEscogerFacturaContingencia", function() {
	
	var idCliente = $(this).attr("idCliente");
	
	window.location = "index.php?ruta=escoger-factura-contingencia&idClienteEscogerFactura="+idCliente;

})

/*=============================================
CARGA BAJO DEMANDA DE CLIENTES EN FACTURACION
=============================================*/

var temporizadorBusquedaClientesFacturacion = null;

function cargarClientesFacturacion(pagina) {

	if ($("#modalVerClientes").length === 0) {
		return;
	}

	var contexto = $("#modalVerClientes").attr("data-contexto") || "facturacion";
	var columnas = contexto === "cotizacion-autorizada" ? 3 : 12;
	var datos = new FormData();
	datos.append("listarClientesFacturacion", "si");
	datos.append("pagina", pagina || 1);
	datos.append("busqueda", $("#buscarClienteFacturacion").val() || "");
	datos.append("contexto", contexto);

	$("#tablaClientesFacturacion").html('<tr><td colspan="' + columnas + '" class="text-center">Cargando clientes...</td></tr>');
	$("#resumenClientesFacturacion").text("Cargando clientes...");
	$("#paginacionClientesFacturacion").empty();

	$.ajax({
		url: "ajax/clientes.ajax.php",
		method: "POST",
		data: datos,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function(respuesta) {
			$("#tablaClientesFacturacion").html(respuesta.html);
			$("#paginacionClientesFacturacion").html(respuesta.paginacion);
			$("#resumenClientesFacturacion").text("Mostrando " + respuesta.mostrando + " de " + respuesta.total + " clientes. Pagina " + respuesta.pagina + " de " + respuesta.totalPaginas + ".");
		},
		error: function() {
			$("#tablaClientesFacturacion").html('<tr><td colspan="' + columnas + '" class="text-center text-danger">No se pudieron cargar los clientes.</td></tr>');
			$("#resumenClientesFacturacion").text("Error al cargar clientes.");
		}
	});
}

$("#modalVerClientes").on("shown.bs.modal", function() {
	cargarClientesFacturacion(1);
});

$("#modalVerClientes").on("hidden.bs.modal", function() {
	clearTimeout(temporizadorBusquedaClientesFacturacion);
	var contexto = $("#modalVerClientes").attr("data-contexto") || "facturacion";
	var columnas = contexto === "cotizacion-autorizada" ? 3 : 12;
	$("#buscarClienteFacturacion").val("");
	$("#tablaClientesFacturacion").html('<tr><td colspan="' + columnas + '" class="text-center">Los clientes se cargaran al abrir este modal.</td></tr>');
	$("#paginacionClientesFacturacion").empty();
	$("#resumenClientesFacturacion").text("Abra el modal para cargar los clientes.");
});

$("#buscarClienteFacturacion").on("input", function() {
	clearTimeout(temporizadorBusquedaClientesFacturacion);
	temporizadorBusquedaClientesFacturacion = setTimeout(function() {
		cargarClientesFacturacion(1);
	}, 300);
});

$("#btnLimpiarBusquedaClientes").on("click", function() {
	$("#buscarClienteFacturacion").val("");
	cargarClientesFacturacion(1);
});

$("#paginacionClientesFacturacion").on("click", ".btnPaginaClientesFacturacion", function() {
	cargarClientesFacturacion($(this).data("pagina"));
});
