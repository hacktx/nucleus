<?hh // strict
/**
 * This file is generated. Do not modify it manually!
 *
 * @generated SignedSource<<11b3e5a2d2b4cab06a9b28d3d924a2e4>>
 */

final class URIMap {

  public function getURIMap(): Map<string, string> {
    return Map {
      '/' => 'FrontpageController',
      '/api/announcements' => 'AnnouncementsApiController',
      '/api/checkin' => 'CheckinApiController',
      '/api/feedback' => 'FeedbackApiController',
      '/api/slack' => 'SlackApiController',
      '/api/user' => 'UserApiController',
      '/dashboard' => 'DashboardController',
      '/invite/accept' => 'AcceptInviteController',
      '/login' => 'LoginController',
      '/members' => 'MembersController',
      '/members/(?<id>\\d+)' => 'SingleMemberController',
      '/members/batch' => 'BatchModifyController',
      '/members/email' => 'EmailController',
      '/members/new' => 'MembersNewController',
      '/oauth/callback' => 'OAuthCallbackController',
      '/settings' => 'SettingsController',
      '/user/confirm' => 'ConfirmUserController',
      '/user/delete' => 'DeleteAccountController',
      '/volunteers' => 'VolunteerController',
    };
  }
}
