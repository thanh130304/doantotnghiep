const translations = {
    en: {
        page_title: "List of Students Registered for Zalo Counseling",
        full_name: "Full Name",
        phone_number: "Phone Number",
        year_of_birth: "Year of Birth",
        facility: "Facility",
        email: "Email"
    },
    vi: {
        page_title: "Danh sách các học sinh đã đăng ký tư vấn qua Zalo",
        full_name: "Họ và Tên",
        phone_number: "Số Điện Thoại",
        year_of_birth: "Năm Sinh",
        facility: "Cơ Sở",
        email: "Email"
    }
};

function changeLanguage() {
    const select = document.getElementById('language-select');
    const language = select.value;
    const translation = translations[language];

    document.getElementById('page-title').textContent = translation.page_title;
    document.querySelectorAll('[data-lang-key]').forEach(element => {
        const key = element.getAttribute('data-lang-key');
        element.textContent = translation[key];
    });
}

document.addEventListener('DOMContentLoaded', () => {
    changeLanguage();
});