<?hh // strict

enum UserState : int {
  Pending = 0;
  Accepted = 1;
  Waitlisted = 2;
  Rejected = 3;
  Confirmed = 4;
}
