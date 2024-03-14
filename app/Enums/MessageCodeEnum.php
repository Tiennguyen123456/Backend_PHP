<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class MessageCodeEnum extends Enum
{
    /* LOGIN STATUS CODE */
    const USER_NAME_OR_PASSWORD_INCORRECT = 'USER_NAME_OR_PASSWORD_INCORRECT'; // Tên đăng nhập hoặc mật khẩu không chính xác
    const LOGIN_SUCCESS = 'LOGIN_SUCCESS'; // Đăng nhập thành công
    const TOO_MANY_LOGIN_ATTEMP = 'TOO_MANY_LOGIN_ATTEMP'; // Quá nhiều lần đăng nhập

    /* HANDLER STATUS CODE */
    const ACCESS_DENIED = 'ACCESS_DENIED'; // Không được phép truy cập
    const PERMISSION_DENIED = 'PERMISSION_DENIED'; // Không được phép thực hiện hành động
    const UNAUTHORIZED_ACTION = 'UNAUTHORIZED_ACTION'; // Hành động không hợp lệ
    const INVALID_HEADER = 'INVALID_HEADER'; // Header không hợp lệ
    const PAGE_NOT_FOUND = 'PAGE_NOT_FOUND'; // Trang không tồn tại
    const TOO_MANY_ATTEMPTS = 'TOO_MANY_ATTEMPTS'; // Quá nhiều lần thử
    const INTERNAL_SERVER_ERROR = 'INTERNAL_SERVER_ERROR'; // Lỗi server
    const METHOD_IS_NOT_SUPPORT = 'METHOD_IS_NOT_SUPPORT'; // Phương thức không hỗ trợ

    /* VALIDATION STATUS CODE */
    const VALIDATION_ERROR = 'VALIDATION_ERROR';

    /* COMMON STATUS CODE */
    const SUCCESS = 'SUCCESS';
    const ERROR = 'ERROR';
    const RESOURCE_NOT_FOUND = 'RESOURCE_NOT_FOUND'; // Không tìm thấy resource
    const USER_NOT_FOUND = 'USER_NOT_FOUND'; // Không tìm thấy user
    const TOKEN_NOT_FOUND = 'TOKEN_NOT_FOUND'; // Không tìm thấy token
    const FAILED_TO_REMOVE = 'FAILED_TO_REMOVE'; // Xóa thất bại
    const FAILED_TO_DELETE = 'FAILED_TO_DELETE'; // Xóa thất bại
    const FAILED_TO_STORE = 'FAILED_TO_STORE'; // Lưu thất bại
    const FILE_UPLOAD_FAILED = 'FILE_UPLOAD_FAILED'; // Upload file thất bại
    const FAILED_TO_UPDATE = 'FAILED_TO_UPDATE';// Cập nhật thất bại
    const EMAIL_NOT_FOUND = 'EMAIL_NOT_FOUND'; // Email không tồn tại

    /* EVENT STATUS CODE */
    const EMAIL_CONTENT_IS_EMPTY = 'EMAIL_CONTENT_IS_EMPTY'; // Nội dung email rỗng
    const CAMPAIGN_NOT_FOUND = 'CAMPAIGN_NOT_FOUND'; // Không tìm thấy chiến dịch
    const FAILED_ACTION = 'FAILED_ACTION'; // Thực hiện thất bại
    const CAMPAIGN_HAS_NO_CLIENT = 'CAMPAIGN_HAS_NO_CLIENT';
    const CAMPAIGN_IS_NOT_NEW_OR_PAUSED = 'CAMPAIGN_IS_NOT_NEW_OR_PAUSED';
    const CAMPAIGN_IS_NOT_RUNNING = 'CAMPAIGN_IS_NOT_RUNNING';
    const CAMPAIGN_IS_ALREADY_STOPPED = 'CAMPAIGN_IS_ALREADY_STOPPED';
}
