function countlen() {
  var arealen = document.editform.plan.value.length;
  var perc = Math.round( arealen * 100 / 60000 );
  if ( perc != window.perc )
  {
    document.editform.perc.value = perc + "%";
    document.editform.filled.width = perc;
    document.editform.unfilled.width = 100 - perc;
    if ( perc >= 100 && window.perc < 100 )
      document.editform.filled.src = "img/danger.gif";
    else if ( perc < 100 && window.perc >= 100 )
      document.editform.filled.src = "img/filled.gif";
    window.perc = perc;
  }
}
window.onload = countlen;
