<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Structure_Translations_Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('structure_translations')->insert([
            'structure_id' => 1,
            'structure_name' => 'Home',
            'locale' => 'en'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 1,
            'structure_name' => 'Trang Chủ',
            'locale' => 'vi'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 2,
            'structure_name' => 'Structure',
            'locale' => 'en',
            'trans_page' => 'a:40:{s:7:"_method";s:3:"PUT";s:6:"_token";s:40:"SGNbC0BJ0dzPxutAsSlx6wOi6ylsyCjepsoHJbKE";s:4:"lang";s:2:"en";s:10:"list_title";s:14:"Structure List";s:4:"root";s:4:"Root";s:3:"add";s:3:"Add";s:4:"edit";s:4:"Edit";s:6:"delete";s:6:"Delete";s:10:"create_new";s:10:"Create New";s:6:"parent";s:6:"Parent";s:22:"structure_name_en_form";s:12:"English Name";s:25:"structure_name_multi_form";s:4:"Name";s:9:"menu_icon";s:4:"Icon";s:4:"sort";s:4:"Sort";s:24:"structure_name_en_holder";s:18:"Input English Name";s:16:"menu_icon_holder";s:15:"Input Menu Icon";s:11:"sort_holder";s:17:"Input Sort Number";s:9:"page_type";s:9:"Page Type";s:4:"menu";s:4:"Menu";s:8:"category";s:8:"Category";s:6:"action";s:6:"Action";s:6:"status";s:6:"Status";s:7:"disable";s:7:"Disable";s:6:"enable";s:6:"Enable";s:6:"update";s:6:"Update";s:12:"back_to_list";s:12:"Back to List";s:26:"structure_name_en_required";s:26:"English Name is required !";s:18:"parent_id_required";s:20:"Parent is required !";s:17:"parent_id_numeric";s:28:"Parent ID must be a number !";s:13:"sort_required";s:18:"Sort is required !";s:12:"sort_numeric";s:23:"Sort must be a number !";s:7:"success";s:9:"Success !";s:5:"error";s:7:"Error !";s:11:"add_success";s:16:"has been added !";s:14:"update_success";s:18:"has been updated !";s:14:"delete_success";s:18:"has been deleted !";s:12:"delete_error";s:30:"have one or more child items !";s:10:"data_error";s:14:"Invalid Data !";s:8:"is_exist";s:18:"is already exits !";s:16:"error_permission";s:40:"You have not permissions for this page !";}'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 2,
            'structure_name' => 'Cấu Trúc Web',
            'locale' => 'vi',
            'trans_page' => 'a:40:{s:7:"_method";s:3:"PUT";s:6:"_token";s:40:"SGNbC0BJ0dzPxutAsSlx6wOi6ylsyCjepsoHJbKE";s:4:"lang";s:2:"vi";s:10:"list_title";s:15:"Cấu Trúc Web";s:4:"root";s:16:"Thư Mục Gốc";s:3:"add";s:5:"Thêm";s:4:"edit";s:5:"Sửa";s:6:"delete";s:4:"Xóa";s:10:"create_new";s:11:"Thêm Mới";s:6:"parent";s:3:"Cha";s:22:"structure_name_en_form";s:16:"Tên Tiếng Anh";s:25:"structure_name_multi_form";s:4:"Tên";s:9:"menu_icon";s:15:"Biểu Tượng";s:4:"sort";s:11:"Sắp Xếp";s:24:"structure_name_en_holder";s:23:"Nhập Tên Tiếng Anh";s:16:"menu_icon_holder";s:22:"Nhập Biểu Tượng";s:11:"sort_holder";s:29:"Nhập Thứ Tự Sắp Xếp";s:9:"page_type";s:6:"Kiểu";s:4:"menu";s:4:"Menu";s:8:"category";s:10:"Danh Mục";s:6:"action";s:9:"Tác Vụ";s:6:"status";s:13:"Trạng Thái";s:7:"disable";s:15:"Vô Hiệu Hóa";s:6:"enable";s:14:"Hoạt Động";s:6:"update";s:12:"Cập Nhật";s:12:"back_to_list";s:20:"Quay về Danh Sách";s:26:"structure_name_en_required";s:35:"Tên Tiếng Anh là bắt buộc !";s:18:"parent_id_required";s:27:"Menu Cha là bắt buộc !";s:17:"parent_id_numeric";s:29:"Id Menu Cha phải là số !";s:13:"sort_required";s:41:"Thứ Tự Sắp Xếp là bắt buộc !";s:12:"sort_numeric";s:40:"Thứ Tự Sắp Xếp phải là số !";s:7:"success";s:14:"Thành Công !";s:5:"error";s:7:"Lỗi !";s:11:"add_success";s:27:"đã được thêm mới !";s:14:"update_success";s:28:"đã được cập nhật !";s:14:"delete_success";s:20:"đã được xóa !";s:12:"delete_error";s:35:"có một hoặc nhiều item con !";s:10:"data_error";s:31:"Dữ liệu không hợp lệ !";s:8:"is_exist";s:18:"đã tồn tại !";s:16:"error_permission";s:58:"Bạn chưa được phân quyền truy cập trang này !";}'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 3,
            'structure_name' => 'Multi Languages',
            'locale' => 'en',
            'trans_page' => 'a:29:{s:7:"_method";s:3:"PUT";s:6:"_token";s:40:"FzTNsBymjZkmCGLXWVWVoKUVnytMR406NwS0GtT1";s:4:"lang";s:2:"en";s:9:"add_title";s:16:"Add New Language";s:6:"update";s:6:"Update";s:12:"back_to_list";s:12:"Back to List";s:10:"list_title";s:13:"Language List";s:13:"structure_url";s:3:"URL";s:6:"status";s:6:"Status";s:6:"action";s:6:"Action";s:6:"enable";s:6:"Enable";s:7:"disable";s:7:"Disable";s:15:"update_language";s:15:"Update Language";s:14:"languages_name";s:13:"Language Name";s:14:"languages_code";s:13:"Language Code";s:11:"name_holder";s:10:"Input Name";s:13:"language_code";s:13:"Language Code";s:20:"language_code_holder";s:19:"Input language Code";s:22:"language_code_required";s:27:"Language Code is required !";s:10:"create_new";s:10:"Create New";s:16:"name_en_required";s:18:"Name is Required !";s:7:"success";s:9:"Success !";s:5:"error";s:7:"Error !";s:11:"add_success";s:16:"has been added !";s:14:"update_success";s:18:"has been updated !";s:14:"delete_success";s:18:"has been deleted !";s:10:"data_error";s:14:"Invalid Data !";s:8:"is_exist";s:18:"is already exits !";s:14:"status_changed";s:18:"has been changed !";}'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 3,
            'structure_name' => 'Đa Ngôn Ngữ',
            'locale' => 'vi',
            'trans_page' => 'a:29:{s:7:"_method";s:3:"PUT";s:6:"_token";s:40:"FzTNsBymjZkmCGLXWVWVoKUVnytMR406NwS0GtT1";s:4:"lang";s:2:"vi";s:9:"add_title";s:23:"Thêm Ngôn Ngữ Mới";s:6:"update";s:12:"Cập nhật";s:12:"back_to_list";s:20:"Quay về Danh Sách";s:10:"list_title";s:22:"Danh sách Ngôn Ngữ";s:13:"structure_url";s:3:"URL";s:6:"status";s:13:"Trạng thái";s:6:"action";s:9:"Tác vụ";s:6:"enable";s:14:"Hoạt động";s:7:"disable";s:15:"Vô hiệu hóa";s:15:"update_language";s:24:"Cập nhật Ngôn Ngữ";s:14:"languages_name";s:16:"Tên Ngôn Ngữ";s:14:"languages_code";s:15:"Mã Ngôn Ngữ";s:11:"name_holder";s:23:"Nhập tên Ngôn Ngữ";s:13:"language_code";s:15:"Mã Ngôn Ngữ";s:20:"language_code_holder";s:22:"Nhập mã Ngôn Ngữ";s:22:"language_code_required";s:34:"Mã Ngôn Ngữ là bắt buộc !";s:10:"create_new";s:11:"Thêm mới";s:16:"name_en_required";s:35:"Tên Ngôn Ngữ là bắt buộc !";s:7:"success";s:14:"Thành Công !";s:5:"error";s:7:"Lỗi !";s:11:"add_success";s:27:"đã được thêm mới !";s:14:"update_success";s:28:"đã được cập nhật !";s:14:"delete_success";s:20:"đã được xóa !";s:10:"data_error";s:31:"Dữ liệu không hợp lệ !";s:8:"is_exist";s:18:"đã tồn tại !";s:14:"status_changed";s:41:"đã được thay đổi trạng thái !";}'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 4,
            'structure_name' => 'Categories',
            'locale' => 'en',
            'trans_page' => 'a:42:{s:7:"_method";s:3:"PUT";s:6:"_token";s:40:"SGXuIMGGCqf5aNSTzImQrSs5Q8bVimKhyq03uy10";s:4:"lang";s:2:"en";s:10:"list_title";s:15:"Categories List";s:9:"add_title";s:19:"Create New Category";s:12:"update_title";s:15:"Update Category";s:4:"root";s:10:"Categories";s:3:"add";s:3:"Add";s:4:"edit";s:4:"Edit";s:6:"delete";s:6:"Delete";s:10:"create_new";s:10:"Create New";s:6:"parent";s:6:"Parent";s:22:"structure_name_en_form";s:12:"English Name";s:25:"structure_name_multi_form";s:4:"Name";s:9:"menu_icon";s:4:"Icon";s:4:"sort";s:4:"Sort";s:24:"structure_name_en_holder";s:18:"Input English Name";s:16:"menu_icon_holder";s:15:"Input Menu Icon";s:11:"sort_holder";s:17:"Input Sort Number";s:9:"page_type";s:9:"Page Type";s:4:"menu";s:4:"Menu";s:8:"category";s:8:"Category";s:6:"action";s:6:"Action";s:6:"status";s:6:"Status";s:7:"disable";s:7:"Disable";s:6:"enable";s:6:"Enable";s:6:"update";s:6:"Update";s:12:"back_to_list";s:12:"Back to List";s:26:"structure_name_en_required";s:26:"English Name is required !";s:18:"parent_id_required";s:20:"Parent is required !";s:17:"parent_id_numeric";s:28:"Parent ID must be a number !";s:13:"sort_required";s:18:"Sort is required !";s:12:"sort_numeric";s:23:"Sort must be a number !";s:7:"success";s:9:"Success !";s:5:"error";s:7:"Error !";s:11:"add_success";s:16:"has been added !";s:14:"update_success";s:18:"has been updated !";s:14:"delete_success";s:18:"has been deleted !";s:12:"delete_error";s:30:"have one or more child items !";s:10:"data_error";s:14:"Invalid Data !";s:8:"is_exist";s:18:"is already exits !";s:16:"error_permission";s:40:"You have not permissions for this page !";}'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 4,
            'structure_name' => 'Danh Mục Sản Phẩm',
            'locale' => 'vi',
            'trans_page' => 'a:42:{s:7:"_method";s:3:"PUT";s:6:"_token";s:40:"SGXuIMGGCqf5aNSTzImQrSs5Q8bVimKhyq03uy10";s:4:"lang";s:2:"vi";s:10:"list_title";s:23:"Danh mục Sản Phẩm";s:9:"add_title";s:29:"Thêm danh mục Sản Phẩm";s:12:"update_title";s:36:"Cập nhật danh mục Sản Phẩm";s:4:"root";s:23:"Danh mục Sản Phẩm";s:3:"add";s:5:"Thêm";s:4:"edit";s:5:"Sửa";s:6:"delete";s:4:"Xóa";s:10:"create_new";s:11:"Thêm Mới";s:6:"parent";s:3:"Cha";s:22:"structure_name_en_form";s:16:"Tên Tiếng Anh";s:25:"structure_name_multi_form";s:4:"Tên";s:9:"menu_icon";s:15:"Biểu Tượng";s:4:"sort";s:11:"Sắp Xếp";s:24:"structure_name_en_holder";s:23:"Nhập Tên Tiếng Anh";s:16:"menu_icon_holder";s:22:"Nhập Biểu Tượng";s:11:"sort_holder";s:29:"Nhập Thứ Tự Sắp Xếp";s:9:"page_type";s:6:"Kiểu";s:4:"menu";s:4:"Menu";s:8:"category";s:10:"Danh Mục";s:6:"action";s:9:"Tác Vụ";s:6:"status";s:13:"Trạng Thái";s:7:"disable";s:15:"Vô Hiệu Hóa";s:6:"enable";s:14:"Hoạt Động";s:6:"update";s:12:"Cập Nhật";s:12:"back_to_list";s:20:"Quay về Danh Sách";s:26:"structure_name_en_required";s:35:"Tên Tiếng Anh là bắt buộc !";s:18:"parent_id_required";s:27:"Menu Cha là bắt buộc !";s:17:"parent_id_numeric";s:29:"Id Menu Cha phải là số !";s:13:"sort_required";s:41:"Thứ Tự Sắp Xếp là bắt buộc !";s:12:"sort_numeric";s:40:"Thứ Tự Sắp Xếp phải là số !";s:7:"success";s:14:"Thành Công !";s:5:"error";s:7:"Lỗi !";s:11:"add_success";s:27:"đã được thêm mới !";s:14:"update_success";s:28:"đã được cập nhật !";s:14:"delete_success";s:20:"đã được xóa !";s:12:"delete_error";s:35:"có một hoặc nhiều item con !";s:10:"data_error";s:31:"Dữ liệu không hợp lệ !";s:8:"is_exist";s:18:"đã tồn tại !";s:16:"error_permission";s:58:"Bạn chưa được phân quyền truy cập trang này !";}'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 5,
            'structure_name' => 'Products',
            'locale' => 'en'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 5,
            'structure_name' => 'Sản Phẩm',
            'locale' => 'vi'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 6,
            'structure_name' => 'Users',
            'locale' => 'en',
            'trans_page' => 'a:47:{s:7:"_method";s:3:"PUT";s:6:"_token";s:40:"FzTNsBymjZkmCGLXWVWVoKUVnytMR406NwS0GtT1";s:4:"lang";s:2:"en";s:10:"list_title";s:10:"Users List";s:6:"avatar";s:6:"Avatar";s:9:"full_name";s:9:"Full Name";s:5:"email";s:5:"Email";s:10:"last_login";s:10:"Last Login";s:6:"status";s:6:"Status";s:6:"action";s:6:"Action";s:9:"add_title";s:15:"Create New User";s:8:"password";s:8:"Password";s:11:"permissions";s:11:"Permissions";s:5:"group";s:5:"Group";s:16:"menus_permission";s:16:"Menus Permission";s:19:"products_permission";s:19:"Products Permission";s:6:"enable";s:6:"Enable";s:7:"disable";s:7:"Disable";s:10:"create_new";s:10:"Create New";s:16:"full_name_holder";s:15:"Input Full Name";s:15:"password_holder";s:14:"Input Password";s:12:"email_holder";s:28:"Input Email type @stdsvn.com";s:18:"full_name_required";s:23:"Full Name is required !";s:14:"email_required";s:19:"Email is required !";s:11:"email_email";s:44:"Email must be in the format xxx@stdsvn.com !";s:17:"password_required";s:22:"Password is required !";s:12:"password_min";s:40:"Password must be at least 8 characters !";s:10:"edit_title";s:9:"Edit User";s:6:"update";s:6:"Update";s:12:"back_to_list";s:12:"Back to List";s:7:"success";s:9:"Success !";s:5:"error";s:7:"Error !";s:11:"add_success";s:16:"has been added !";s:14:"update_success";s:18:"has been updated !";s:14:"delete_success";s:18:"has been deleted !";s:10:"data_error";s:14:"Invalid Data !";s:8:"is_exist";s:18:"is already exits !";s:15:"error_extension";s:21:"Invalid image style !";s:11:"error_image";s:23:"Avatar mus be a image !";s:14:"status_changed";s:25:"status has been changed !";s:16:"list_group_title";s:16:"Users Group List";s:15:"add_group_title";s:13:"Add New Group";s:16:"edit_group_title";s:16:"Edit Users Group";s:4:"name";s:4:"Name";s:11:"name_holder";s:16:"Input Group name";s:13:"name_required";s:24:"Group name is required !";s:14:"group_required";s:19:"Choose user group !";}'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 6,
            'structure_name' => 'Người Dùng',
            'locale' => 'vi',
            'trans_page' => 'a:47:{s:7:"_method";s:3:"PUT";s:6:"_token";s:40:"FzTNsBymjZkmCGLXWVWVoKUVnytMR406NwS0GtT1";s:4:"lang";s:2:"vi";s:10:"list_title";s:25:"Danh sách Người Dùng";s:6:"avatar";s:19:"Ảnh Đại Diện";s:9:"full_name";s:9:"Họ Tên";s:5:"email";s:5:"Email";s:10:"last_login";s:26:"Đăng Nhập Gần Nhất";s:6:"status";s:13:"Trạng Thái";s:6:"action";s:9:"Tác Vụ";s:9:"add_title";s:26:"Thêm Người Dùng Mới";s:8:"password";s:12:"Mật Khẩu";s:11:"permissions";s:13:"Phân Quyền";s:5:"group";s:5:"Nhóm";s:16:"menus_permission";s:18:"Phân Quyền Menu";s:19:"products_permission";s:26:"Phân Quyền Sản Phẩm";s:6:"enable";s:14:"Hoạt Động";s:7:"disable";s:15:"Vô Hiệu Hóa";s:10:"create_new";s:11:"Thêm Mới";s:16:"full_name_holder";s:16:"Nhập họ tên";s:15:"password_holder";s:19:"Nhập mật khẩu";s:12:"email_holder";s:12:"Nhập email";s:18:"full_name_required";s:28:"Họ tên là bắt buộc !";s:14:"email_required";s:24:"Email là bắt buộc !";s:11:"email_email";s:46:"Email phải có định dạng xxx@stdsvn.com";s:17:"password_required";s:31:"Mật khẩu là bắt buộc !";s:12:"password_min";s:47:"Mật khẩu phải có ít nhất 8 ký tự !";s:10:"edit_title";s:37:"Thay đổi thông tin Người Dùng";s:6:"update";s:12:"Cập Nhật";s:12:"back_to_list";s:20:"Quay về Danh Sách";s:7:"success";s:14:"Thành công !";s:5:"error";s:7:"Lỗi !";s:11:"add_success";s:27:"đã được thêm mới !";s:14:"update_success";s:28:"đã được cập nhật !";s:14:"delete_success";s:20:"đã được xóa !";s:10:"data_error";s:31:"Dữ liệu không hợp lệ !";s:8:"is_exist";s:18:"đã tồn tại !";s:15:"error_extension";s:42:"File avatar không đúng định dạng !";s:11:"error_image";s:36:"Avatar phải là file hình ảnh !";s:14:"status_changed";s:41:"đã được thay đổi trạng thái !";s:16:"list_group_title";s:31:"Danh sách Nhóm Người Dùng";s:15:"add_group_title";s:26:"Thêm Nhóm Người Dùng";s:16:"edit_group_title";s:33:"Tùy chỉnh Nhóm Người Dùng";s:4:"name";s:4:"Tên";s:11:"name_holder";s:17:"Nhập tên nhóm";s:13:"name_required";s:29:"Tên nhóm là bắt buộc !";s:14:"group_required";s:29:"Chọn nhóm người dùng !";}'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 7,
            'structure_name' => 'Account',
            'locale' => 'en',
            'trans_page' => 'a:25:{s:7:"_method";s:3:"PUT";s:6:"_token";s:40:"59XrcE6kngCvrvIb3juL66HQj8cqLJEWSwYxIAEG";s:4:"lang";s:2:"en";s:10:"show_title";s:12:"User Profile";s:9:"full_name";s:9:"Full Name";s:5:"email";s:5:"Email";s:8:"password";s:8:"Password";s:15:"profile_picture";s:15:"Profile Picture";s:4:"edit";s:4:"Edit";s:10:"edit_title";s:12:"Edit Account";s:16:"full_name_holder";s:20:"Input Your Full Name";s:12:"email_holder";s:16:"Input Your Email";s:15:"password_holder";s:45:"Input Your Password with at least 8 character";s:18:"full_name_required";s:23:"Full Name is required !";s:14:"email_required";s:19:"Email is required !";s:11:"email_email";s:44:"Email must be in the format xxx@stdsvn.com !";s:17:"password_required";s:22:"Password is required !";s:12:"password_min";s:40:"Password must be at least 8 characters !";s:6:"update";s:6:"Update";s:12:"back_to_list";s:12:"Back to List";s:9:"not_exist";s:29:"Your account does not exist !";s:10:"email_used";s:15:"has been used !";s:7:"success";s:9:"Success !";s:14:"update_success";s:18:"has been updated !";s:5:"error";s:7:"Error !";}'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 7,
            'structure_name' => 'Tài Khoản',
            'locale' => 'vi',
            'trans_page' => 'a:25:{s:7:"_method";s:3:"PUT";s:6:"_token";s:40:"59XrcE6kngCvrvIb3juL66HQj8cqLJEWSwYxIAEG";s:4:"lang";s:2:"vi";s:10:"show_title";s:23:"Thông Tin Tài Khoản";s:9:"full_name";s:9:"Họ Tên";s:5:"email";s:5:"Email";s:8:"password";s:12:"Mật Khẩu";s:15:"profile_picture";s:19:"Ảnh đại diện";s:4:"edit";s:5:"Sửa";s:10:"edit_title";s:25:"Cập nhật Tài Khoản";s:16:"full_name_holder";s:28:"Nhập họ tên của bạn";s:12:"email_holder";s:24:"Nhập Email của bạn";s:15:"password_holder";s:47:"Nhập mật khẩu với ít nhất 8 ký tự";s:18:"full_name_required";s:28:"Họ tên là bắt buộc !";s:14:"email_required";s:24:"Email là bắt buộc !";s:11:"email_email";s:46:"Email phải có định dạng xxx@stdsvn.com";s:17:"password_required";s:31:"Mật khẩu là bắt buộc !";s:12:"password_min";s:47:"Mật khẩu phải có ít nhất 8 ký tự !";s:6:"update";s:12:"Cập nhật";s:12:"back_to_list";s:20:"Quay về Danh Sách";s:9:"not_exist";s:33:"Tài khoản không tồn tại !";s:10:"email_used";s:27:"đã được sử dụng !";s:7:"success";s:14:"Thành công !";s:14:"update_success";s:28:"đã được cập nhật !";s:5:"error";s:7:"Lỗi !";}'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 8,
            'structure_name' => 'Change Language',
            'locale' => 'en'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 8,
            'structure_name' => 'Đổi Ngôn Ngữ',
            'locale' => 'vi'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 9,
            'structure_name' => 'Login',
            'locale' => 'en'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 9,
            'structure_name' => 'Đăng nhập',
            'locale' => 'vi'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 10,
            'structure_name' => 'Register',
            'locale' => 'en'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 10,
            'structure_name' => 'Đăng ký',
            'locale' => 'vi'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 11,
            'structure_name' => 'Forgot Password',
            'locale' => 'en'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 11,
            'structure_name' => 'Quên mật khẩu',
            'locale' => 'vi'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 12,
            'structure_name' => 'Add',
            'locale' => 'en'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 12,
            'structure_name' => 'Thêm',
            'locale' => 'vi'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 13,
            'structure_name' => 'Edit',
            'locale' => 'en'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 13,
            'structure_name' => 'Sửa',
            'locale' => 'vi'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 14,
            'structure_name' => 'Delete',
            'locale' => 'en'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 14,
            'structure_name' => 'Xóa',
            'locale' => 'vi'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 15,
            'structure_name' => 'Languages',
            'locale' => 'en'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 15,
            'structure_name' => 'Ngôn Ngữ',
            'locale' => 'vi'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 16,
            'structure_name' => 'Translations',
            'locale' => 'en'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 16,
            'structure_name' => 'Dịch Ngôn Ngữ',
            'locale' => 'vi'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 17,
            'structure_name' => 'Edit',
            'locale' => 'en'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 17,
            'structure_name' => 'Sửa',
            'locale' => 'vi'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 21,
            'structure_name' => 'Users Group',
            'locale' => 'en'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 21,
            'structure_name' => 'Nhóm Người Dùng',
            'locale' => 'vi'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 22,
            'structure_name' => 'Users List',
            'locale' => 'en'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 22,
            'structure_name' => 'Danh sách Người Dùng',
            'locale' => 'vi'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 18,
            'structure_name' => 'Add',
            'locale' => 'en'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 18,
            'structure_name' => 'Thêm',
            'locale' => 'vi'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 19,
            'structure_name' => 'Edit',
            'locale' => 'en'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 19,
            'structure_name' => 'Sửa',
            'locale' => 'vi'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 20,
            'structure_name' => 'Delete',
            'locale' => 'en'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 20,
            'structure_name' => 'Xóa',
            'locale' => 'vi'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 26,
            'structure_name' => 'Add',
            'locale' => 'en'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 26,
            'structure_name' => 'Thêm',
            'locale' => 'vi'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 27,
            'structure_name' => 'Edit',
            'locale' => 'en'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 27,
            'structure_name' => 'Sửa',
            'locale' => 'vi'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 28,
            'structure_name' => 'Delete',
            'locale' => 'en'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 28,
            'structure_name' => 'Xóa',
            'locale' => 'vi'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 23,
            'structure_name' => 'Add',
            'locale' => 'en'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 23,
            'structure_name' => 'Thêm',
            'locale' => 'vi'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 24,
            'structure_name' => 'Edit',
            'locale' => 'en'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 24,
            'structure_name' => 'Sửa',
            'locale' => 'vi'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 25,
            'structure_name' => 'Delete',
            'locale' => 'en'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 25,
            'structure_name' => 'Xóa',
            'locale' => 'vi'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 29,
            'structure_name' => 'EN',
            'locale' => 'en'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 29,
            'structure_name' => 'EN',
            'locale' => 'vi'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 30,
            'structure_name' => 'VI',
            'locale' => 'en'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 30,
            'structure_name' => 'VI',
            'locale' => 'vi'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 31,
            'structure_name' => 'Change Status',
            'locale' => 'en'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 31,
            'structure_name' => 'Đổi Trạng Thái',
            'locale' => 'vi'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 32,
            'structure_name' => 'Change Status',
            'locale' => 'en'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 32,
            'structure_name' => 'Đổi Trạng Thái',
            'locale' => 'vi'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 33,
            'structure_name' => 'Change Status',
            'locale' => 'en'
        ]);

        DB::table('structure_translations')->insert([
            'structure_id' => 33,
            'structure_name' => 'Đổi Trạng Thái',
            'locale' => 'vi'
        ]);
    }
}
