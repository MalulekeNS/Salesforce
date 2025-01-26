document.addEventListener("DOMContentLoaded", function () {
    const toggleSidebarBtn = document.querySelector(".toggle-sidebar");
    const sidebar = document.querySelector(".sidebar");

    toggleSidebarBtn.addEventListener("click", function () {
        sidebar.classList.toggle("open");
    });
});
