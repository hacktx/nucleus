<?hh // strict
/**
 * This file is generated. Do not modify it manually!
 *
 * @generated SignedSource<<adcc484f777a7bfe4e5ad637c884bb18>>
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
      '/oauth/callback' => 'OAuthCallbackController',
      '/settings' => 'SettingsController',
      '/user/confirm' => 'ConfirmUserController',
      '/user/delete' => 'DeleteAccountController',
      '/volunteers' => 'VolunteerController',
    };
  }
}
