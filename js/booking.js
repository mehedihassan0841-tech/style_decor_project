document.querySelectorAll(".booking-delete").forEach(button=>{

button.onclick=function(){

let id=this.dataset.id;

Swal.fire({

title:"Delete Booking?",

text:"This booking will be permanently deleted.",

icon:"warning",

showCancelButton:true,

confirmButtonColor:"#dc2626",

cancelButtonColor:"#64748b",

confirmButtonText:"Yes Delete"

}).then((result)=>{

if(result.isConfirmed){

window.location="delete_booking.php?id="+id;

}

});

};

});