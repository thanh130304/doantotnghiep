// Hiện cảnh báo khi người dùng nhấn nút submit nhưng thiếu thông tin (client-side)
document.querySelector("form").addEventListener("submit", function(e) {
    const inputs = document.querySelectorAll("input[required]");
    for (let input of inputs) {
        if (!input.value.trim()) {
            alert("Vui lòng điền đầy đủ thông tin!");
            input.focus();
            e.preventDefault();
            return;
        }
    }
});
