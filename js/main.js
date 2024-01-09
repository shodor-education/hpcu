---
---
function selectBrowseOption() {
  const filters = [
    {% for filter-values in site.data.resource-filter-values %}
      "{{ filter-values[0] }}"
      {% unless forloop.last %},{% endunless %}
    {% endfor %}
  ];
  const selectedResources = Array.prototype.slice.call(
    document.getElementsByClassName("resource")
  );
  for (let i = 0; i < selectedResources.length; i++) {
    selectedResources[i].style.display = "block";
  }
  for (let i = 0; i < filters.length; i++) {
    const filter = filters[i];
    const filterType = document.querySelector(
      "input[type='radio'][name='" + filter + "-filter-type']:checked"
    ).value;
    const checkboxes = document.querySelectorAll(
      "input[type='checkbox'][name='" + filter + "-options']:checked"
    );
    for (let j = 0; j < selectedResources.length; j++) {
      const resource = selectedResources[j];
      const filterValues = resource.dataset[filter].split(",");
      let hide = (filterType == "OR" && checkboxes.length > 0);
      for (let k = 0; k < checkboxes.length; k++) {
        const match = filterValues.includes(checkboxes[k].value);
        if (match && filterType == "OR") {
          hide = false;
          break;
        }
        else if (!match && filterType == "AND") {
          hide = true;
          break;
        }
      }
      if (hide) {
        resource.style.display = "none";
        selectedResources.splice(j, 1);
        j--;
      }
    }
  }
  document.getElementById("results-count").innerHTML
    = selectedResources.length + " result"
    + (selectedResources.length != 1 ? "s" : "");
  if (selectedResources.length > 0) {
    selectedResources[0].style.borderTop = "0";
    for (let i = 1; i < selectedResources.length; i++) {
      selectedResources[i].style.borderTop = "";
    }
  }
}
