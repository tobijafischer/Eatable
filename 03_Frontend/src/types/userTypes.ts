export interface SingleUser {
  id: string;
  email: string;
  firstname?: string;
  lastname?: string;
  username: string;
  joined: string;
  favorite_shops: string | null;
  favorite_recipes: string | null;
  avatar: string | null;
}