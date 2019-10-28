<?php declare(strict_types = 1);
namespace Kuke\Http;
class HttpCode
{
    /**
     * @Message("服务器错误")
     */
    const SERVER_ERROR = 500;
    /**
     * @Message("请求成功")
     */
    const SERVER_OK = 200;
    /**
     * @Message("用户登录异常")
     */
    const SERVER_USER_LOGIN_ERROR = 205;
    /**
     * @Message("重定向")
     */
    const SERVER_REDIRECT = 302;
    /**
     * @Message("执行错误")
     */
    const SERVER_REQUEST_ERROR = 400;
    /**
     * @Message("无法连接到socket服务器")
     */
    const Server_Not_Available = 'Server_Not_Available';
    /**
     * @Message("网断了")
     */
    const Error_Internet_Disconnected = 'Error_Internet_Disconnected';
    /**
     * @Message("连接未建立")
     */
    const Error_Connection_is_not_Established = 'Error_Connection_is_not_Established';
    /**
     * @Message("超时")
     */
    const Error_Timeout = 'Error_Timeout';
    /**
     * @Message("参数错误")
     */
    const Param_Error = 'Param_Error';
    /**
     * @Message("请选择文件")
     */
    const No_File_Selected = 'No_File_Selected';
    /**
     * @Message("文件类型错误")
     */
    const Wrong_File_Type = 'Wrong_File_Type';
    /**
     * @Message("文件过大")
     */
    const File_Too_Large = 'File_Too_Large';
    /**
     * @Message("不能获取跨域Iframe的内容")
     */
    const Cross_Origin_Iframe = 'Cross_Origin_Iframe';
    /**
     * @Message("未知错误")
     */
    const Error_Unknown = 'Error_Unknown';
}
