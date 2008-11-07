function hideMessage() {
  var message = document.getElementById('message');
  message.style.visibility = "hidden";
}
function valRadioButton(radio_button)
{
  for (i=radio_button.length-1; i > -1; i--) {
    if (radio_button[i].checked) {
      option = i;
    }
  }
  return radio_button[option].value;
}

function valSelect(select)
{
  return select.options[select.selectedIndex].value;
}
