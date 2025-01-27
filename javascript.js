document.querySelector('.toggle-sidebar').addEventListener('click', function() {
    document.querySelector('.sidebar-container').classList.toggle('open');
    document.querySelector('.content').classList.toggle('sidebar-open');
    document.querySelector('.sidebar-overlay').classList.toggle('active');
});

document.querySelector('.sidebar-overlay').addEventListener('click', function() {
    document.querySelector('.sidebar-container').classList.remove('open');
    document.querySelector('.content').classList.remove('sidebar-open');
    this.classList.remove('active');
});
