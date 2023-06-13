export interface SingleRecipePartner {
  id: string;
  title: string;
  slug: string | null;
  image: string;
  url: string | null;
  partner_type_id: string;
  active: string;
}

export interface SingleRecipeTag {
  id: number;
  title: string;
  slug: string | null;
  image: string;
  active: number;
}

export interface SingleRecipeCategory {
  id: number;
  title: string;
  slug: string | null;
  image: string;
  description: string | null;
  active: number;
}