document.addEventListener('dblclick', function(event) {
    alert("Double-click disabled!");
    event.preventDefault();
    event.stopPropagation();
  }, true //capturing phase!!
);
