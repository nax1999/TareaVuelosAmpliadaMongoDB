/* Configuración (rutas y +) */

var miRuta;

//miRuta = prompt ("Copia y pega la ruta de tu 'puerta de entrada'");

if (miRuta != "") {
  miRuta = "http://localhost/carla/pruebamongo/Servidor/billetes.php";
}

console.log("URL del servidor:" + miRuta);

/* Buscar Vuelos */

function buscarVuelos() {

  //console.log('Inicio carga datos');

  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      
      console.log("Respuesta: " + this.responseText);

      try {
        let myObj = JSON.parse(this.responseText);

        if (myObj.estado == true) {
          pintaDatos(myObj.encontrados, myObj.vuelos, myObj.busqueda);
          var mensaje = "Datos cargados con éxito";
          pintaMensaje(true, mensaje, 1500);
        } else {
          pintaMensaje(false,myObj.mensaje);
        }

      } catch (e) {
        let arrayMensaje = {
          "Error": "El JSON recibido tiene un error de sintaxis. No se puede parsear",
          "JSON Recibido": this.responseText
        };
        pintaMensaje(false, arrayMensaje);
      }




    } else {
      if (this.status == 404 && this.readyState == 4) {
        let arrayMensaje = {
          "Error 404": "No se encuentra la página del servidor. Revisa la url",
          "Ruta": miRuta
        };
        pintaMensaje(false, arrayMensaje);
      }

    }
  };


  // Establecemos parámetros de búsqueda (si los hay)

  let rutaBusqueda = calculaRuta(false);
  xhttp.open("GET", rutaBusqueda, true);
  xhttp.send();

  //console.log('Fin carga datos');

}

/* 
  Función para añadir parámetros a la búsqueda
  GET en el caso de que el servidor sea PHP
  "magic params" en el caso de node (ya lo veremos)
*/

function calculaRuta(magicParams) {
  let rutaAuxiliar = miRuta;
  let fecha = document.getElementById("fechaInput").value;
  let origen = document.getElementById("origenInput").value;
  let destino = document.getElementById("destinoInput").value;

  if (!magicParams) {
    let contador = 0;
    if (fecha != "") {
      rutaAuxiliar += '?fecha=' + fecha;
      contador++;
    }
    if (origen != "") {
      if (contador == 0) rutaAuxiliar += '?'
      else rutaAuxiliar += '&';
      rutaAuxiliar += 'origen=' + origen;
      contador++;
    }
    if (destino != "") {
      if (contador == 0) rutaAuxiliar += '?'
      else rutaAuxiliar += '&';
      rutaAuxiliar += 'destino=' + destino;
      contador++;
    }
  }

  console.log("Peticion: " + rutaAuxiliar);
  return rutaAuxiliar;
}

/*
    Función que recorre el array de vuelos que hemos recibido del servidor
    y los va pintando por pantalla
*/

function pintaDatos(numero, arrayvuelos, datosBusqueda) {

  var contenedor = document.getElementById("contenedor");
  contenedor.innerHTML = "";

  var capaQuery = document.getElementById("capaQuery");
  capaQuery.innerHTML = "";
  let parrafoBusqueda = document.createElement("p");
  let textoBusqueda;
  if (datosBusqueda != null) {

    textoBusqueda = "Encontrados " + numero + " vuelos para ";
    for (let x in datosBusqueda) {
      let textoPropiedad = x.replace("_", " ") + ": ";
      textoBusqueda += textoPropiedad;

      let texto = datosBusqueda[x];
      textoBusqueda += texto + " ";
    }
    let nodeTexto = document.createTextNode(textoBusqueda);
    parrafoBusqueda.appendChild(nodeTexto);
  } else {
    textoBusqueda = document.createTextNode("SE MUESTRAN TODOS LOS VUELOS");
    parrafoBusqueda.appendChild(textoBusqueda);
  }

  capaQuery.appendChild(parrafoBusqueda);

  for (let i = 0; i < numero; i++) {

    var miVuelo = arrayvuelos[i];
    pintaVuelo(miVuelo, contenedor);

  }

}


/*
   Función auxiliar para pintar en pantalla un vuelo
*/

function pintaVuelo(miVuelo, contenedor) {

  var x;

  var capaVuelo = document.createElement("article");
  capaVuelo.setAttribute("id", "vuelo_" + miVuelo.codigo);
  capaVuelo.setAttribute("onclick", "pasaDatos(" + miVuelo.codigo + ")");
  capaVuelo.setAttribute("class", "capaVuelo");
  contenedor.appendChild(capaVuelo);

  // Cada propiedad del vuelo que venga en el JSON la añadimos

  for (x in miVuelo) {

    let propiedad = document.createElement("span");

    let elemento;
    if (x == "codigo") {
      elemento = document.createElement("h3");
      // Creamos un botón para cada vuelo (por si nos interesa ocultarlo)
      var boton = document.createElement("i");
      boton.setAttribute("onclick", "ocultar('" + miVuelo.codigo + "')");
      boton.setAttribute("class", "fas fa-eye-slash botonEliminar");
      elemento.appendChild(boton);
    } else {
      let textoPropiedad = document.createTextNode(x.replace("_", " ") + ": ");
      propiedad.appendChild(textoPropiedad);
      elemento = document.createElement("p");
    }

    var valor = document.createElement("span");
    valor.setAttribute("id", x + "_" + miVuelo.codigo);
    var texto = document.createTextNode(miVuelo[x]);
    valor.appendChild(texto);

    elemento.appendChild(propiedad);
    elemento.appendChild(valor);

    capaVuelo.appendChild(elemento);
  }

}

function ocultar(idVuelo) {

  var capaVueloBorrar = document.getElementById("vuelo_" + idVuelo);
  var contenedor = document.getElementById("contenedor");
  contenedor.removeChild(capaVueloBorrar);

}

/* Función auxiliar para pintar mensaje en un modal 
   (errores y mensajes al usuario)
   tipo => booleano (true ha ido bien, false hay error)
   mensaje => simple o array asociativo de mensajes
 */

function pintaMensaje(tipo, mensaje, tiempo) {

  let modalTitle = document.getElementById("h2modal");
  if (tipo) {
    modalTitle.innerHTML = "ÉXITO!";
    modalTitle.style.backgroundColor = "green";
    if (tiempo != null) setTimeout(function () { modal.style.display = "none"; }, tiempo);
  } else {
    modalTitle.innerHTML = "ERROR";
    modalTitle.style.backgroundColor = "red";
  }

  let modalBody = document.getElementById("modal-body");
  modalBody.innerHTML = "";

  if ((typeof mensaje === 'object' || typeof mensaje === 'array') && mensaje !== null) {
    for (let x in mensaje) {
      let pError = document.createElement("p");
      console.log(mensaje[x]);
      let tError = document.createTextNode(x + ": " + mensaje[x]);
      pError.appendChild(tError);
      modalBody.appendChild(pError);
    }
  } else {
    let pError = document.createElement("p");
    let tError = document.createTextNode(mensaje);
    pError.appendChild(tError);
    modalBody.appendChild(pError);
  }


  openModal();
}


/* Al pinchar en un vuelo (en la capa)
  se pasan los datos al formulario de compra */

function pasaDatos(idCapa) {

  console.log("Pasando datos de capa a formulario de compra");

  // //alert(idCapa);
  // var miNombre = document.getElementById("nombre_" + idCapa).innerHTML;
  // var miDescripcion = document.getElementById("descripcion_" + idCapa).innerHTML;
  // var miCaracteristica = document.getElementById("caracteristica_" + idCapa).innerHTML;
  // var miEdad = document.getElementById("edad_" + idCapa).innerHTML;

  // document.getElementById('idInput').value = idCapa;
  // document.getElementById('nombreInput').value = miNombre;
  // document.getElementById('caracteristicaInput').value = miCaracteristica;
  // document.getElementById('descripcionInput').value = miDescripcion;
  // document.getElementById('edadInput').value = miEdad;

}

function comprarBillete() {

  var peticion = JSON.stringify(capturaDatosForm());

  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      
      console.log("Respuesta: " + this.responseText);

      try {
        let myObj = JSON.parse(this.responseText);

        if (myObj.estado == true) {
          var mensaje = "Compra realizada con éxito";
          pintaMensaje(true, mensaje);
        } else {
          pintaMensaje(false,myObj.mensaje);
        }

      } catch (e) {
        let arrayMensaje = {
          "Error": "El JSON recibido tiene un error de sintaxis. No se puede parsear",
          "JSON Recibido": this.responseText
        };
        pintaMensaje(false, arrayMensaje);
      }

    } else {
      if (this.status == 404 && this.readyState == 4) {
        let arrayMensaje = {
          "Error 404": "No se encuentra la página del servidor. Revisa la url",
          "Ruta": miRuta
        };
        pintaMensaje(false, arrayMensaje);
      }

    }
  };

  xhttp.open("POST", miRuta, true);
  xhttp.setRequestHeader("Content-type", "application/json");

  xhttp.send(peticion);

}

/*
  Función para capturar los datos de un formulario
  y meterlos todos en una variable
*/

function capturaDatosForm() {

  let elements = document.querySelectorAll("#ccmForm input[type=text]")
  let datos = {};
  for (var i = 0, element; element = elements[i++];) {
    if (element.value != "") {
      datos[element.name] = element.value;
    }
  }

  console.log("EN CAPTURA DATOS FORM");
  console.log(datos);
  return datos;

}
