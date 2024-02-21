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
    const USER_NAME_OR_PASSWORD_INCORRECT = 'USER_NAME_OR_PASSWORD_INCORRECT';
    const LOGIN_SUCCESS = 'LOGIN_SUCCESS';
    const TOO_MANY_LOGIN_ATTEMP = 'TOO_MANY_LOGIN_ATTEMP';

    /* HANDLER STATUS CODE */
    const ACCESS_DENIED = 'ACCESS_DENIED';
    const PERMISSION_DENIED = 'PERMISSION_DENIED';
    const UNAUTHORIZED_ACTION = 'UNAUTHORIZED_ACTION';
    const INVALID_HEADER = 'INVALID_HEADER';
    const PAGE_NOT_FOUND = 'PAGE_NOT_FOUND';
    const TOO_MANY_ATTEMPTS = 'TOO_MANY_ATTEMPTS';
    const INTERNAL_SERVER_ERROR = 'INTERNAL_SERVER_ERROR';
    const METHOD_IS_NOT_SUPPORT = 'METHOD_IS_NOT_SUPPORT';

    /* VALIDATION STATUS CODE */
    const VALIDATION_ERROR = 'VALIDATION_ERROR';

    /* COMMON STATUS CODE */
    const SUCCESS = 'SUCCESS';
    const RESOURCE_NOT_FOUND = 'RESOURCE_NOT_FOUND';
    const USER_NOT_FOUND = 'USER_NOT_FOUND';
    const TOKEN_NOT_FOUND = 'TOKEN_NOT_FOUND';
    const FAILED_TO_REMOVE = 'FAILED_TO_REMOVE';
    const FAILED_TO_DELETE = 'FAILED_TO_DELETE';
    const FAILED_TO_STORE = 'FAILED_TO_STORE';
    const FILE_UPLOAD_FAILED = 'FILE_UPLOAD_FAILED';
    const FAILED_TO_UPDATE = 'FAILED_TO_UPDATE';
}
