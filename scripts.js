// Function to open the Add Property Modal
function openAddPropertyModal() {
    document.getElementById('addPropertyModal').style.display = 'block';
}

// Function to close the Add Property Modal
function closeAddPropertyModal() {
    document.getElementById('addPropertyModal').style.display = 'none';
}

// Function to open the edit property modal and load property data
function openEditPropertyModal(propertyId) {
    document.getElementById('editPropertyId').value = propertyId; // Set property ID in hidden field

    // Send AJAX request to fetch property data and populate the form fields
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'get_property_details.php?id=' + propertyId, true);
    xhr.onload = function () {
        if (xhr.status === 200) {
            var property = JSON.parse(xhr.responseText);
            document.getElementById('editPropertyTitle').value = property.title;
            document.getElementById('editPropertyDescription').value = property.description;
            document.getElementById('editPropertyPrice').value = property.price;
            document.getElementById('editPropertyLocation').value = property.location;
            document.getElementById('editPropertyAddress').value = property.address;
            document.getElementById('editPropertyBedrooms').value = property.bedrooms;
            document.getElementById('editPropertyGarages').value = property.garage;
            document.getElementById('editPropertySize').value = property.size;
            document.getElementById('editPropertyStatus').value = property.status;
        }
    };
    xhr.send();

    // Open the modal
    document.getElementById('editPropertyModal').style.display = 'block';
}

// Function to close the edit property modal
function closeEditPropertyModal() {
    document.getElementById('editPropertyModal').style.display = 'none';
}

// Close modals if user clicks outside the modal content
window.onclick = function(event) {
    const addModal = document.getElementById('addPropertyModal');
    const editModal = document.getElementById('editPropertyModal');
    
    if (event.target == addModal) {
        addModal.style.display = 'none';
    }
    
    if (event.target == editModal) {
        editModal.style.display = 'none';
    }
}
// Delete Property Function
function deleteProperty() {
    alert('Property deleted successfully!');
}

function openAddUserModal() {
    document.getElementById('addUserModal').style.display = 'block';
}

function closeAddUserModal() {
    document.getElementById('addUserModal').style.display = 'none';
}

//function openEditUserModal(userId) {
    // Fetch user data from server or data attributes and populate the form
//    document.getElementById('editUserId').value = userId;
    // Other form fields should be populated as needed
//    document.getElementById('editUserModal').style.display = 'block';
//}

//function closeEditUserModal() {
//    document.getElementById('editUserModal').style.display = 'none';
//}

function openEditUserModal(userId) {
    // Fetch user details from the server
    fetch(`get_user_details.php?id=${userId}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert("User not found.");
            } else {
                // Populate fields with the fetched data
                document.getElementById('editUserId').value = data.id;
                document.getElementById('editName').value = data.full_name;
                document.getElementById('editUserName').value = data.username;
                document.getElementById('editEmail').value = data.email;
                document.getElementById('editUserRole').value = data.role;
                
                // Display the modal
                document.getElementById('editUserModal').style.display = 'block';
            }
        })
        .catch(error => console.error('Error fetching user data:', error));
}

// Close the edit user modal
function closeEditUserModal() {
    document.getElementById('editUserModal').style.display = 'none';
}



function deleteUser(userId) {
    if (confirm("Are you sure you want to delete this user?")) {
        // Send a POST request to delete the user
        fetch('delete_user.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'userId=' + userId
        })
        .then(response => response.text())
        .then(data => {
            alert(data);
            location.reload(); // Reload the page to reflect changes
        })
        .catch(error => console.error('Error:', error));
    }
}

function openReplyModal(button) {
    // Get the inquiry data from the clicked button's parent element
    const enquiryItem = button.closest('.enquiry-item');
    const inquiryUsername = enquiryItem.getAttribute('data-inquiry-username');
    const inquiryMessage = enquiryItem.getAttribute('data-inquiry-message');
    const inquiryProperty = enquiryItem.getAttribute('data-inquiry-property');

    // Populate the modal fields with the inquiry data
    document.getElementById('inquiryUsername').value = inquiryUsername;
    document.getElementById('inquiryProperty').value = inquiryProperty;
    document.getElementById('inquiryMessage').value = inquiryMessage;

    // Display the modal
    document.getElementById('replyModal').style.display = 'block';
}

function closeReplyModal() {
    document.getElementById('replyModal').style.display = 'none';
}

// Visitor Analytics Chart
//const ctx1 = document.getElementById('visitorChart').getContext('2d');
//const visitorChart = new Chart(ctx1, {
//    type: 'line',
//    data: {
  //      labels: ['January', 'February', 'March', 'April', 'May', 'June'],
    //    datasets: [{
      //      label: 'Visitors',
        //    data: [50, 100, 100, 200, 250, 300],
          //  backgroundColor: 'rgba(75, 192, 192, 0.2)',
            //borderColor: 'rgba(75, 192, 192, 1)',
            //borderWidth: 1
       // }]
    //},
    //options: {
      //  responsive: true,
       // scales: {
        //    y: {
         //       beginAtZero: true
          //  }
       // }
   // }
//});

// Revenue Statistics Chart
//const ctx2 = document.getElementById('revenueChart').getContext('2d');
//const revenueChart = new Chart(ctx2, {
//    type: 'bar',
//    data: {
//        labels: ['January', 'February', 'March', 'April', 'May', 'June'],
//        datasets: [{
 //           label: 'Revenue',
//            data: [300, 500, 400, 600, 700, 800],
//            backgroundColor: 'rgba(153, 102, 255, 0.2)',
 //           borderColor: 'rgba(153, 102, 255, 1)',
//            borderWidth: 1
//        }]
//    },
//    options: {
//        responsive: true,
//        scales: {
//            y: {
//                beginAtZero: true
//            }
//        }
//    }
//});

document.addEventListener("DOMContentLoaded", function () {
    // Fetch the data from the server
    fetch('dashboard_data.php')
        .then(response => response.json())
        .then(data => {
            // Update total properties
            document.querySelector('.card:nth-child(1) p').textContent = data.total_properties;

            // Update sold properties
            document.querySelector('.card:nth-child(2) p').textContent = data.sold_properties;

            // Render Visitor Analytics Chart
            const visitorLabels = data.visitor_analytics.map(item => item.date);
            const visitorData = data.visitor_analytics.map(item => item.users);
            const visitorChartCtx = document.getElementById('visitorChart').getContext('2d');
            new Chart(visitorChartCtx, {
                type: 'line',
                data: {
                    labels: visitorLabels,
                    datasets: [{
                        label: 'Users Registered',
                        data: visitorData,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Render Revenue Statistics Chart
            const revenueLabels = data.monthly_revenue.map(item => `Month ${item.month}`);
            const revenueData = data.monthly_revenue.map(item => item.total_revenue);
            const revenueChartCtx = document.getElementById('revenueChart').getContext('2d');
            new Chart(revenueChartCtx, {
                type: 'bar',
                data: {
                    labels: revenueLabels,
                    datasets: [{
                        label: 'Total Revenue',
                        data: revenueData,
                        backgroundColor: 'rgba(153, 102, 255, 0.2)',
                        borderColor: 'rgba(153, 102, 255, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    } 
                }
            });
        })
        .catch(error => console.error('Error fetching dashboard data:', error));
});

// Open Add Agent Modal
function openAddAgentModal() {
    document.getElementById('addAgentModal').style.display = 'block';
}

// Close Add Agent Modal
function closeAddAgentModal() {
    document.getElementById('addAgentModal').style.display = 'none';
}

// Open Edit Agent Modal
function openEditAgentModal(agentId) {
    document.getElementById('editAgentId').value = agentId; // Set agent ID in hidden field

    // Send AJAX request to fetch agent data and populate the form fields
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'get_agent_details.php?id=' + agentId, true);
    xhr.onload = function () {
        if (xhr.status === 200) {
            var agent = JSON.parse(xhr.responseText);
            document.getElementById('editName').value = agent.full_name;
            document.getElementById('editUserName').value = agent.username;
            document.getElementById('editEmail').value = agent.email;
            
        }
    };
    xhr.send();

    document.getElementById('editAgentModal').style.display = 'block';
}

// Close Edit Agent Modal
function closeEditAgentModal() {
    document.getElementById('editAgentModal').style.display = 'none';
}

// Delete Agent Function
function deleteAgent(agentId) {
    if (confirm("Are you sure you want to delete this agent?")) {
        var formData = new FormData();
        formData.append('agentId', agentId);

        fetch('delete_agent.php', {
            method: 'POST',
            body: formData
        }).then(response => response.text())
          .then(data => {
                alert(data);
              location.reload();
          })
          .catch(error => console.error('Error:', error));
    }
}

// Function to display success message
function showMessage(message) {
    alert(message);
}

document.addEventListener("DOMContentLoaded", function () {
    // Fetch data from analytics_data.php
    fetch('analytics_data.php')
        .then(response => response.json())
        .then(data => {
            renderVisitorChart(data.visitor_data);
            renderRevenueChart(data.enquiry_data);
        })
        .catch(error => console.error('Error fetching analytics data:', error));
});

// Render Visitor Analytics Chart (Users Registered per Month)
function renderVisitorChart(visitorData) {
    const months = [
        'January', 'February', 'March', 'April', 'May', 
        'June', 'July', 'August', 'September', 'October', 
        'November', 'December'
    ];

    const labels = visitorData.map(item => months[item.month - 1]);
    const registrations = visitorData.map(item => item.registrations);

    const ctx = document.getElementById('visitorChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Users Registered',
                data: registrations,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

// Render Revenue Statistics Chart (Enquiry Statuses)
function renderRevenueChart(enquiryData) {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Pending Enquiries', 'Responded Enquiries'],
            datasets: [{
                label: 'Enquiries',
                data: [enquiryData.pending, enquiryData.responded],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

document.addEventListener("DOMContentLoaded", function () {
    // Fetch analytics data from the PHP script
    fetch('analytics_data.php')
        .then(response => response.json())
        .then(data => {
            renderVisitorChart(data.visitor_data);
            renderRevenueChart(data.enquiry_data);
        })
        .catch(error => console.error('Error fetching analytics data:', error));
});

// Function to render the line chart for visitor analytics
function renderVisitorChart(visitorData) {
    const ctx = document.getElementById('visitorChart').getContext('2d');
    const visitorChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: visitorData.map(data => new Date(data.year, data.month - 1).toLocaleString('default', { month: 'long' })),
            datasets: [{
                label: 'Users Registered',
                data: visitorData.map(data => data.registrations),
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

// Function to render the pie chart for revenue statistics
function renderRevenueChart(enquiryData) {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Pending Enquiries', 'Responded Enquiries'],
            datasets: [{
                label: 'Enquiry Status',
                data: [enquiryData.pending, enquiryData.responded],
                backgroundColor: [
                    'rgba(255, 205, 86, 0.2)',
                    'rgba(54, 162, 235, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 205, 86, 1)',
                    'rgba(54, 162, 235, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true
        }
    });
}

