<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['zalo_form'])) {
        // Xử lý form Zalo
        $full_name = $_POST['zalo_full_name'];
        $phone_number = $_POST['zalo_phone_number'];
        $year_of_birth = $_POST['zalo_year_of_birth'];
        $facility = $_POST['zalo_facility'];
        $email = $_POST['zalo_email'];

        $conn = new mysqli("localhost", "root", "", "btec_db");
        if ($conn->connect_error) {
            die("Kết nối thất bại: " . $conn->connect_error);
        }

        $stmt = $conn->prepare("INSERT INTO users (full_name, phone_number, year_of_birth, facility, email) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiss", $full_name, $phone_number, $year_of_birth, $facility, $email);

        if ($stmt->execute()) {
            // Chuyển hướng đến Zalo sau khi lưu thành công
            header("Location: https://zalo.me/0839783612");
            exit();
        } else {
            echo "<p style='color:red;'>Lỗi: " . $stmt->error . "</p>";
        }

        $stmt->close();
        $conn->close();
    } else {
        // Xử lý form tư vấn hiện tại
        $full_name = $_POST['full_name'];
        $phone_number = $_POST['phone_number'];
        $email = $_POST['email'];
        $date_of_birth = $_POST['date_of_birth'];
        $campus = $_POST['campus'];
        $program_of_interest = $_POST['program_of_interest'];
        $notes = $_POST['notes'];

        $conn = new mysqli("localhost", "root", "", "btec_db");
        if ($conn->connect_error) {
            die("Kết nối thất bại: " . $conn->connect_error);
        }

        $stmt = $conn->prepare("INSERT INTO registrations (full_name, phone_number, email, date_of_birth, campus, program_of_interest, notes) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $full_name, $phone_number, $email, $date_of_birth, $campus, $program_of_interest, $notes);

        if ($stmt->execute()) {
            echo "<p style='color:green;'>Đăng ký thành công!</p>";
        } else {
            echo "<p style='color:red;'>Lỗi: " . $stmt->error . "</p>";
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-i18n="title">BTEC FPT - Student Portal</title>
    <link rel="icon" type="image/jpeg" href="434553129_925189299614872_2549948014377488514_n.jpg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
    <link rel="stylesheet" href="index.css">
    <script src="index.js" defer></script>
</head>
<body class="bg-white">
    <header class="fixed top-0 left-0 right-0 bg-white shadow-sm z-50">
        <div class="mx-auto">
            <nav class="flex items-center justify-between h-16 px-6">
                <div class="flex items-center gap-12">
                    <a href="index.php" id="logo" class="hover:opacity-80 transition-opacity">
                        <img src="https://static.readdy.ai/image/47f28debd40be05a67a829bfbe537201/b9dfa7c7f53ecaad622f45e283f1cad1.png" alt="BTEC FPT Logo" class="h-8">
                    </a>
                    <div class="flex items-center gap-2">
                        <a href="#" class="text-sm font-medium text-primary rounded-full px-4 py-1.5 bg-primary/5" data-i18n="home">Home</a>
                        <div class="relative group">
                            <button class="text-sm font-medium text-gray-900 hover:text-primary rounded-full px-4 py-1.5 hover:bg-primary/5 flex items-center gap-1">
                                <span data-i18n="about">About</span>
                                <i class="ri-arrow-down-s-line"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a href="#" data-i18n="about_fpt_education">About FPT Education</a>
                                <a href="#" data-i18n="about_polytechnic">About FPT Polytechnic College</a>
                                <a href="#" data-i18n="about_btec_fpt">About BTEC FPT</a>
                                <a href="#" data-i18n="about_vision_mission">Message - Vision - Mission</a>
                                <a href="#" data-i18n="about_departments">Department Information</a>
                                <a href="#" data-i18n="about_recruitment">Recruitment</a>
                                <a href="#" data-i18n="about_brochure">Online Brochure</a>
                                <a href="#" data-i18n="about_handbook">Student Handbook</a>
                            </div>
                        </div>
                        <div class="relative group">
                            <button class="text-sm font-medium text-gray-900 hover:text-primary rounded-full px-4 py-1.5 hover:bg-primary/5 flex items-center gap-1">
                                <span data-i18n="programs">Programs</span>
                                <i class="ri-arrow-down-s-line"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a href="javascript:void(0)" onclick="scrollToTrainingPrograms()" data-i18n="program_it">Information Technology</a>
                                <a href="javascript:void(0)" onclick="scrollToTrainingPrograms()" data-i18n="program_business">Business Administration</a>
                                <a href="javascript:void(0)" onclick="scrollToTrainingPrograms()" data-i18n="program_design">Graphic Design</a>
                            </div>
                        </div>
                        <div class="relative group">
                            <button class="text-sm font-medium text-gray-900 hover:text-primary rounded-full px-4 py-1.5 hover:bg-primary/5 flex items-center gap-1">
                                <span data-i18n="tuition">Tuition & Scholarships</span>
                                <i class="ri-arrow-down-s-line"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a href="#" data-i18n="tuition_fees">Tuition Fees</a>
                                <a href="#" data-i18n="tuition_scholarships">Scholarships</a>
                            </div>
                        </div>
                        <div class="relative group">
                            <button class="text-sm font-medium text-gray-900 hover:text-primary rounded-full px-4 py-1.5 hover:bg-primary/5 flex items-center gap-1">
                                <span data-i18n="admission">Admission</span>
                                <i class="ri-arrow-down-s-line"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a href="#" data-i18n="admission_info">Admission Information</a>
                                <a href="#" data-i18n="admission_online">Online Registration</a>
                                <a href="#" data-i18n="admission_regulations">Admission Regulations</a>
                                <a href="#" data-i18n="admission_guide">Admission Guide</a>
                                <a href="#" data-i18n="admission_scholarships">Tuition Scholarships</a>
                                <a href="#" data-i18n="admission_form">Enrollment Form</a>
                                <a href="#" data-i18n="admission_faq">Frequently Asked Questions</a>
                                <a href="#" data-i18n="admission_contact">Contact</a>
                            </div>
                        </div>
                        <div class="relative group">
                            <button class="text-sm font-medium text-gray-900 hover:text-primary rounded-full px-4 py-1.5 hover:bg-primary/5 flex items-center gap-1">
                                <span data-i18n="students">Students</span>
                                <i class="ri-arrow-down-s-line"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a href="#" data-i18n="students_faces">BTEC Faces</a>
                                <a href="#" data-i18n="students_alumni">Alumni</a>
                                <a href="#" data-i18n="students_activities">Student Activities</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-6">
                    <div class="relative">
                        <div class="flex items-center gap-2 px-3 py-1.5 bg-gray-50 rounded-full">
                            <i class="ri-search-line text-gray-400"></i>
                            <input type="text" data-i18n="search_placeholder" placeholder="Search..." class="bg-transparent text-sm focus:outline-none w-48">
                        </div>
                    </div>
                    <select id="language-select" class="text-sm font-medium text-gray-900 bg-gray-50 border border-gray-300 rounded-full px-4 py-1.5 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        <option value="en">English</option>
                        <option value="vi">Vietnamese</option>
                    </select>
                </div>
            </nav>
        </div>
    </header>
    <main class="pt-16">
        <section class="relative h-[600px] overflow-hidden">
            <div class="absolute inset-0" style="background-image: url('https://readdy.ai/api/search-image?query=modern%20university%20campus%20with%20students%20studying%20in%20a%20bright%2C%20contemporary%20learning%20environment.%20The%20scene%20shows%20a%20mix%20of%20indoor%20and%20outdoor%20spaces%20with%20glass%20walls%2C%20modern%20architecture%2C%20and%20natural%20light.%20Students%20are%20engaged%20in%20collaborative%20work.%20The%20image%20has%20a%20clean%2C%20professional%20aesthetic%20with%20a%20light%2C%20airy%20atmosphere&width=1920&height=1080&seq=1&orientation=landscape'); background-size: cover; background-position: center;">
            </div>
            <div class="hero-overlay absolute inset-0"></div>
            <div class="container mx-auto px-4 h-full relative">
                <div class="flex items-center h-full">
                    <div class="max-w-2xl">
                        <h1 class="text-5xl font-bold text-gray-900 mb-6" data-i18n="hero_title">Discover Your Future at BTEC FPT</h1>
                        <p class="text-xl text-gray-700 mb-8" data-i18n="hero_subtitle">High-quality international training programs, modern learning environment, and global job opportunities</p>
                        <div class="flex gap-4">
                            <button onclick="document.getElementById('consult-section').scrollIntoView({ behavior: 'smooth' })" class="bg-primary text-white px-6 py-3 !rounded-button hover:bg-primary/90" data-i18n="register_now">Register Now</button>
                            <button class="border border-primary text-primary px-6 py-3 !rounded-button hover:bg-primary/5" data-i18n="learn_more">Learn More</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="py-20 bg-gray-50">
            <div class="container mx-auto px-4">
                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900 mb-6" data-i18n="about_section_title">About BTEC FPT</h2>
                        <p class="text-gray-700 mb-6" data-i18n="about_section_desc">BTEC FPT combines Pearson’s international BTEC training program (UK) with FPT Corporation, a pioneer in technology and education in Vietnam.</p>
                        <div class="space-y-4">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 flex items-center justify-center rounded-full bg-primary/10 text-primary">
                                    <i class="ri-focus-3-line text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900" data-i18n="vision">Vision</h3>
                                    <p class="text-gray-700" data-i18n="vision_desc">To become a leading training institution in technology and business in Vietnam</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 flex items-center justify-center rounded-full bg-primary/10 text-primary">
                                    <i class="ri-flag-line text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900" data-i18n="mission">Mission</h3>
                                    <p class="text-gray-700" data-i18n="mission_desc">To train high-quality human resources to meet the needs of businesses and society</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="relative h-[400px] rounded-lg overflow-hidden">
                        <img src="https://readdy.ai/api/search-image?query=modern%20university%20classroom%20with%20students%20engaged%20in%20interactive%20learning.%20The%20space%20features%20contemporary%20furniture%2C%20technology%20integration%2C%20and%20collaborative%20workspaces.%20The%20scene%20is%20well-lit%20with%20natural%20light%20from%20large%20windows%2C%20creating%20an%20inspiring%20educational%20environment&width=800&height=600&seq=2&orientation=landscape" class="absolute inset-0 w-full h-full object-cover" alt="BTEC FPT Campus">
                    </div>
                </div>
            </div>
        </section>
        <section role="training-programs" class="py-20">
            <div class="container mx-auto px-4">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4" data-i18n="programs_section_title">Training Programs</h2>
                    <p class="text-gray-700 max-w-2xl mx-auto" data-i18n="programs_section_desc">Explore our diverse training programs designed to meet real-world market demands</p>
                </div>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                        <div class="h-48 relative">
                            <img src="https://readdy.ai/api/search-image?query=modern%20IT%20professionals%20working%20in%20a%20contemporary%20office%20environment%20with%20multiple%20computer%20screens%20showing%20code%20and%20software%20development%20interfaces.%20The%20scene%20emphasizes%20technology%20and%20professional%20collaboration&width=600&height=400&seq=3&orientation=landscape" class="absolute inset-0 w-full h-full object-cover" alt="IT">
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-semibold text-gray-900 mb-2" data-i18n="it_program">Information Technology</h3>
                            <p class="text-gray-700 mb-4" data-i18n="it_program_desc">Comprehensive training in programming, software development, and IT project management</p>
                            <button class="text-primary hover:text-primary/90 font-medium flex items-center gap-1" data-i18n="view_details">View Details <i class="ri-arrow-right-line"></i></button>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                        <div class="h-48 relative">
                            <img src="https://cdn2.fptshop.com.vn/unsafe/1920x0/filters:format(webp):quality(75)/2024_3_27_638471250632330672_graphic-design-la-gi.jpg" class="absolute inset-0 w-full h-full object-cover" alt="Design">
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-semibold text-gray-900 mb-2" data-i18n="design_program">Graphic Design</h3>
                            <p class="text-gray-700 mb-4" data-i18n="design_program_desc">Develop creative and professional skills in digital design and communication</p>
                            <button class="text-primary hover:text-primary/90 font-medium flex items-center gap-1" data-i18n="view_details">View Details <i class="ri-arrow-right-line"></i></button>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                        <div class="h-48 relative">
                            <img src="https://readdy.ai/api/search-image?query=business%20professionals%20in%20a%20modern%20corporate%20meeting%20room%20discussing%20strategy%20with%20charts%20and%20presentations.%20The%20environment%20reflects%20contemporary%20business%20practices%20and%20professional%20collaboration&width=600&height=400&seq=5&orientation=landscape" class="absolute inset-0 w-full h-full object-cover" alt="Business">
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-semibold text-gray-900 mb-2" data-i18n="business_program">Business Administration</h3>
                            <p class="text-gray-700 mb-4" data-i18n="business_program_desc">Equip knowledge and skills in management, marketing, and business development</p>
                            <button class="text-primary hover:text-primary/90 font-medium flex items-center gap-1" data-i18n="view_details">View Details <i class="ri-arrow-right-line"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section id="consult-section" class="py-20 bg-gray-50">
            <div class="container mx-auto px-4">
                <div class="grid lg:grid-cols-2 gap-8 items-start">
                    <div class="relative h-[450px] rounded-lg overflow-hidden hidden lg:block">
                        <img src="https://btec.fpt.edu.vn/wp-content/themes/monatheme/btec-tuyen-sinh-2023/images/mobile.jpg" class="absolute inset-0 w-full h-full object-cover" alt="BTEC FPT Consultation">
                    </div>
                    <div class="max-w-3xl lg:max-w-none mx-auto">
                        <div class="text-center mb-12">
                            <h2 class="text-3xl font-bold text-gray-900 mb-4" data-i18n="consult_section_title">Register for Consultation</h2>
                            <p class="text-gray-700" data-i18n="consult_section_desc">Leave your information to receive detailed consultation about our programs</p>
                        </div>
                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="space-y-6">
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2" data-i18n="full_name">Full Name</label>
                                    <input type="text" name="full_name" class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary" data-i18n="full_name_placeholder" placeholder="Enter your full name" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2" data-i18n="phone">Phone Number</label>
                                    <input type="tel" name="phone_number" class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary" data-i18n="phone_placeholder" placeholder="Enter your phone number" required>
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2" data-i18n="email">Email</label>
                                    <input type="email" name="email" class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary" data-i18n="email_placeholder" placeholder="Enter your email address" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2" data-i18n="dob">Date of Birth</label>
                                    <input type="date" name="date_of_birth" class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary" data-i18n="dob_placeholder" placeholder="Select your date of birth" required>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2" data-i18n="campus">Campus</label>
                                <select name="campus" class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary" data-i18n="campus_placeholder" required>
                                    <option value="" data-i18n="campus_placeholder">Select a campus</option>
                                    <option value="hanoi" data-i18n="campus_hanoi">Hanoi</option>
                                    <option value="danang" data-i18n="campus_danang">Da Nang</option>
                                    <option value="hcm" data-i18n="campus_hcm">Ho Chi Minh City</option>
                                    <option value="cantho" data-i18n="campus_can_tho">Can Tho</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2" data-i18n="program_interest">Program of Interest</label>
                                <select name="program_of_interest" class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary" data-i18n="program_interest_placeholder" required>
                                    <option value="" data-i18n="program_interest_placeholder">Select a program</option>
                                    <option value="cntt" data-i18n="it_option">Information Technology</option>
                                    <option value="design" data-i18n="design_option">Graphic Design</option>
                                    <option value="business" data-i18n="business_option">Business Administration</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2" data-i18n="notes">Notes</label>
                                <textarea name="notes" class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary" rows="4" data-i18n="notes_placeholder" placeholder="Enter consultation details"></textarea>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="bg-primary text-white px-8 py-3 !rounded-button hover:bg-primary/90" data-i18n="submit">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <footer class="bg-gray-900 text-gray-400 py-12">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-white text-lg font-semibold mb-4" data-i18n="footer_about_title">About BTEC FPT</h3>
                    <p class="mb-4" data-i18n="footer_about_desc">Training high-quality human resources to international standards</p>
                    <div class="flex space-x-4">
                        <a href="#" class="hover:text-white">
                            <div class="w-8 h-8 flex items-center justify-center">
                                <i class="ri-facebook-fill"></i>
                            </div>
                        </a>
                        <a href="#" class="hover:text-white">
                            <div class="w-8 h-8 flex items-center justify-center">
                                <i class="ri-youtube-fill"></i>
                            </div>
                        </a>
                        <a href="#" class="hover:text-white">
                            <div class="w-8 h-8 flex items-center justify-center">
                                <i class="ri-linkedin-fill"></i>
                            </div>
                        </a>
                    </div>
                </div>
                <div>
                    <h3 class="text-white text-lg font-semibold mb-4" data-i18n="footer_contact_title">Contact</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                        <div>
                            <h3 class="text-orange-500 font-semibold mb-1" data-i18n="footer_hanoi">BTEC FPT Hanoi</h3>
                            <p data-i18n="footer_hanoi_address">BTEC FPT Building, Trinh Van Bo, Nam Tu Liem, Hanoi</p>
                            <p data-i18n="footer_hanoi_phone">098 109 05 13</p>
                        </div>
                        <div>
                            <h3 class="text-orange-500 font-semibold mb-1" data-i18n="footer_danang">BTEC FPT Da Nang</h3>
                            <p data-i18n="footer_danang_address">66 Vo Van Tan, Thanh Khe, Da Nang</p>
                            <p data-i18n="footer_danang_phone">032 757 57 27</p>
                        </div>
                        <div>
                            <h3 class="text-orange-500 font-semibold mb-1" data-i18n="footer_hcm">BTEC FPT Ho Chi Minh</h3>
                            <p data-i18n="footer_hcm_address">F Building, Quang Trung Software Park, Tan Chanh Hiep, District 12, Ho Chi Minh City</p>
                            <p data-i18n="footer_hcm_phone">035 385 21 38</p>
                        </div>
                        <div>
                            <h3 class="text-orange-500 font-semibold mb-1" data-i18n="footer_can_tho">BTEC FPT Can Tho</h3>
                            <p data-i18n="footer_can_tho_address">Street No. 22, Thuong Thanh Ward, Cai Rang District, Can Tho</p>
                            <p data-i18n="footer_can_tho_phone">096 705 76 05</p>
                        </div>
                    </div>
                </div>
                <div>
                    <h3 class="text-white text-lg font-semibold mb-4" data-i18n="footer_info_title">Information</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="hover:text-white" data-i18n="footer_info_admission">Admission</a></li>
                        <li><a href="#" class="hover:text-white" data-i18n="footer_info_programs">Training Programs</a></li>
                        <li><a href="#" class="hover:text-white" data-i18n="footer_info_tuition">Tuition - Scholarships</a></li>
                        <li><a href="#" class="hover:text-white" data-i18n="footer_info_news">News - Events</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-white text-lg font-semibold mb-4" data-i18n="footer_subscribe_title">Subscribe to Newsletter</h3>
                    <p class="mb-4" data-i18n="footer_subscribe_desc">Receive the latest updates on training programs and admission</p>
                    <div class="flex gap-2">
                        <input type="email" data-i18n="footer_subscribe_placeholder" placeholder="Your email" class="flex-1 px-4 py-2 bg-gray-800 border border-gray-700 rounded focus:outline-none focus:border-primary">
                        <button class="bg-primary text-white px-4 py-2 !rounded-button hover:bg-primary/90" data-i18n="footer_subscribe_button">Subscribe</button>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-12 pt-8 text-center">
                <p data-i18n="footer_copyright">© 2025 BTEC FPT. All rights reserved.</p>
            </div>
        </div>
    </footer>
    <div id="contact-icons" style="position: fixed; bottom: 20px; right: 20px; z-index: 100000;">
        <div class="chat-zalo"><a href="javascript:void(0)" onclick="openZaloForm()"><img src="Icon/zalo-icon.png" alt="zalo-icon" width="50" height="50" /></a></div>
    </div>
    <!-- Modal Form Zalo -->
    <div id="zaloModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-[100001]">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h2 class="text-2xl font-bold text-gray-900 mb-4" data-i18n="zalo_form_title">Đăng ký tư vấn qua Zalo</h2>
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="space-y-4">
                <input type="hidden" name="zalo_form" value="1">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2" data-i18n="full_name">Họ và tên</label>
                    <input type="text" name="zalo_full_name" class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary" data-i18n="full_name_placeholder" placeholder="Nhập họ và tên" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2" data-i18n="phone">Số điện thoại</label>
                    <input type="tel" name="zalo_phone_number" class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary" data-i18n="phone_placeholder" placeholder="Nhập số điện thoại" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2" data-i18n="year_of_birth">Năm sinh</label>
                    <input type="number" name="zalo_year_of_birth" class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary" min="1900" max="2025" data-i18n="year_of_birth_placeholder" placeholder="Nhập năm sinh" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2" data-i18n="facility">Cơ sở quan tâm</label>
                    <select name="zalo_facility" class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary" data-i18n="facility_placeholder" required>
                        <option value="" data-i18n="facility_placeholder">Chọn cơ sở</option>
                        <option value="Hà Nội" data-i18n="facility_hanoi">Hà Nội</option>
                        <option value="Đà Nẵng" data-i18n="facility_danang">Đà Nẵng</option>
                        <option value="TP.Hồ Chí Minh" data-i18n="facility_hcm">TP.Hồ Chí Minh</option>
                        <option value="Cần Thơ" data-i18n="facility_can_tho">Cần Thơ</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2" data-i18n="email">Email</label>
                    <input type="email" name="zalo_email" class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary" data-i18n="email_placeholder" placeholder="Nhập địa chỉ email" required>
                </div>
                <div class="text-center">
                    <button type="submit" class="bg-primary text-white px-8 py-3 !rounded-button hover:bg-primary/90" data-i18n="start_conversation">Bắt đầu hội thoại</button>
                </div>
            </form>
            <button onclick="closeZaloForm()" class="mt-4 text-gray-600 hover:text-gray-900">Đóng</button>
        </div>
    </div>
</body>
</html>