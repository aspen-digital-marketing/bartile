$(document).ready(function () {
  // Attach a click event handler to the links in the All_tabs
  $(".All_tabs a").click(function (e) {
    e.preventDefault();

    // Get the data-link attribute value of the clicked link
    var clickedLink = $(this).data("link");

    // Hide all content boxes initially
    $(".AllFrom_boxes").addClass("d-none");

    // Show the content box that matches the clicked link's data-link attribute
    $(".AllFrom_boxes[data-link='" + clickedLink + "']").removeClass("d-none");

    // Remove the 'active' class from all links and add it to the clicked link
    $(".All_tabs a").removeClass("active_From-btn");
    $(this).addClass("active_From-btn");
  });

  // Trigger click on the first link to initially display its content
  $(".All_tabs a:first").click();
});

// Define your Airtable API Key and Base ID
const apiKey = "keykAL79KCRT3q6MA";
const baseId = "applH4zAKIPxiOLth";

function addToAirtable(tableName, dataToWrite) {
  const apiUrl = `https://api.airtable.com/v0/${baseId}/${tableName}`;

  // Set headers and send a POST request
  $.ajax({
    url: apiUrl,
    method: "POST",
    headers: {
      Authorization: `Bearer ${apiKey}`,
      "Content-Type": "application/json",
    },
    data: JSON.stringify({ fields: dataToWrite }),
    success: function (response) {
      console.log("Data added to Airtable:", response);
      // You can perform further actions here if needed
    },
    error: function (error) {
      console.error("Error adding data to Airtable:", error);
    },
  });
}

// Add an event listener to your form submission
// document
//   .getElementById("new-england-slate-form")
//   .addEventListener("submit", function (event) {
//     event.preventDefault(); // Prevent the default form submission

//     // Get the form data
//     const formData = new FormData(this);

//     // Create an object to hold the form data
//     const formDataObject = {};
//     formData.forEach((value, key) => {
//       formDataObject[key] = value;
//     });

//     // Specify the table name based on your tab or form
//     const tableName = "Bartile";
//     console.log("formDataObject", formDataObject);
//     // Create an object with the form data
//     const dataToWrite = {
//       //TitleName: formDataObject.distributor, // Example field name
//       Distributor: formDataObject.distributor,
//       DistributorDate: formDataObject.distributor_date,
//       Contractor: formDataObject.Contractor,
//       color: formDataObject.color,
//       customer: formDataObject.Customer,
//     };

//     // Call the function to add data to Airtable with the dynamic table name
//     addToAirtable(tableName, dataToWrite);
//     console.log("dataToWrite", dataToWrite);
//   });
