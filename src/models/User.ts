export type UserProfileToken = {
  id: string;
  name: string;
  phone: string;
  token: string;
};

export type UserProfile = {
  id: string;
  name: string;
  phone: string;
  partner_id?: string;
  partner_name?: string;
};
