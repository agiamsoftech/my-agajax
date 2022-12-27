// console.log("hello");

let fetchBtn = document.getElementById('fetchBtn');
fetchBtn.addEventListener('click', buttonClickHandler);

function buttonClickHandler() {

    document.getElementById('list').style.display = 'none';
    document.getElementById('country').style.display = 'none';
    $('.mycdcollection').hide();  
    let headerlist = document.getElementById('headerlist');
    msg = '<h1 id="headerlist">Fetch List</h1>';
    headerlist.innerHTML = msg;
    console.log("fetch");

    // Instantiate an xhr Object
    const xhr = new XMLHttpRequest();

    // Open the object
    // xhr.open('GET', 'harry.txt', true);
    // xhr.open('GET', 'https://jsonplaceholder.typicode.com/todos/1', true);
    xhr.open('POST', 'http://dummy.restapiexample.com/api/v1/create', true);
    xhr.getResponseHeader('Content-type', 'application/json');
        
    xhr.onprogress = function() {
        console.log('On Progress'); // for spinner
    }

    // xhr.onreadystatechange = function() {
    //     console.log('ready state is ', xhr.readyState);
    // }

    xhr.onload = function() {
        if(this.status === 200){
            console.log(this.responseText);
            document.getElementById("demo_fetchBtn").innerHTML = this.responseText;
        }
        else{
            console.log('error');
        }        
    }

    // send the Request
    params = ` {"name":"test","salary":"123","age":"23"}`;
    xhr.send(params);


}

let popBtn = document.getElementById('popBtn');
popBtn.addEventListener('click', popHandler);

function popHandler() {

    document.getElementById('country').style.display = 'none';
    $('.mycdcollection').hide();  
    
    const xhr = new XMLHttpRequest();
    
    xhr.open('GET', 'http://dummy.restapiexample.com/api/v1/employees', true);
    
    xhr.onload = function() {
        if(this.status === 200){
            let headerlist = document.getElementById('headerlist');            
            // alert(headerlist);
            let obj = JSON.parse(this.responseText);
            // console.log(obj['data']);
            msg = '<h1 id="headerlist">List Employee</h1>';
            headerlist.innerHTML = msg;
            let list = document.getElementById('list');
            str = "";            
            let i=0;
            for (i=0;i<obj['data'].length;i++)
            {
                // console.log(obj['data'][i]);
                // str += `<li>${obj['data'][i].employee_name} </li>`;
                // <a href="#demo" data-toggle="collapse">Simple collapsible</a>
                // <div id="demo" class="collapse">
                //     Lorem
                // </div>employee_age
                str += '<li><a href="#empid'+ obj['data'][i]['id'] +'" data-toggle="collapse">' + obj['data'][i]['id'] + ' - ' + obj['data'][i]['employee_name'] + '</a><ul id="empid'+ obj['data'][i]['id'] +'" class="collapse"><li> Name: '  + obj['data'][i]['employee_name'] + '</li><li> Sallary: ' + obj['data'][i]['employee_salary'] + '</li><li> Age: ' + obj['data'][i]['employee_age'] + '</li></ul></li>';
            }
            document.getElementById('list').style.display = '';
            list.innerHTML = str;
        }
        else{
            console.log('error');
        }        
    }

    xhr.send();

}

let btnCountry = document.getElementById('btnCountry');
btnCountry.addEventListener('click', fetchCountry);

function fetchCountry() {
    // alert('str');
    // let list = document.getElementById('list').style.display = "none";
    let headerlist = document.getElementById('headerlist');
    msg = '<h1 id="headerlist">List Country</h1>';
    headerlist.innerHTML = msg;
    document.getElementById('list').style.display = 'none'; 
    $('.mycdcollection').hide();  
    document.getElementsByTagName("H1")[0].removeAttribute("class"); 
	$.ajax({
        url: "country.php",
        type: "POST",
        data: {servid: 1},        
        success:function(msgs){  
            // alert(msgs);
            // $('.ssc').removeClass('current');
        	document.getElementById('country').innerHTML = msgs;
            document.getElementById('country').style.display = '';
        	// $('.ssc').removeClass('current');
        	// $('#listid'+str).addClass('current');
        	
        }
    });
}

// AJAX XML Example By ALI
function loadDoc() {
    document.getElementById('list').style.display = 'none';
    document.getElementById('country').style.display = 'none';
    document.getElementById('demo_fetchBtn').style.display = 'none';
    let headerlist = document.getElementById('headerlist');
    msg = '<h1 id="headerlist">My CD Collection</h1>';
    headerlist.innerHTML = msg;    
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      myFunction(this);
    }
    };
    xhttp.open("GET", "cd_catalog.xml", true);
    xhttp.send();
}
function myFunction(xml) {
  var i;
  var xmlDoc = xml.responseXML;
  var table="<tr><th>Artist</th><th>Title</th><th>Country</th></tr>";
  var x = xmlDoc.getElementsByTagName("CD");
  for (i = 0; i <x.length; i++) { 
    table += "<tr><td>" +
    x[i].getElementsByTagName("ARTIST")[0].childNodes[0].nodeValue +
    "</td><td>" +
    x[i].getElementsByTagName("TITLE")[0].childNodes[0].nodeValue +
    "</td><td>" +
    x[i].getElementsByTagName("COUNTRY")[0].childNodes[0].nodeValue +
    "</td></tr>";
  }
  $('.mycdcollection').show();
  document.getElementById("demo_mycdcollection").innerHTML = table;
}