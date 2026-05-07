export type RoomAmenity = {
    id: number;
    name: string;
    icon: string | null;
};

export type RoomSummary = {
    id: number;
    name: string;
    slug: string;
    type: 'single' | 'double' | 'suite' | 'deluxe';
    type_label: string;
    price_per_night: number;
    capacity: number;
    floor: number;
    status: 'available' | 'unavailable';
    thumbnail: string | null;
    description_excerpt: string;
    average_rating: number | null;
    reviews_count: number;
    amenities?: RoomAmenity[];
};

export type RoomReview = {
    id: number;
    rating: number;
    comment: string | null;
    created_at: string;
    guest_name?: string;
};

export type RoomDetail = Omit<RoomSummary, 'description_excerpt'> & {
    description: string;
    images: string[];
    amenities: RoomAmenity[];
    reviews: RoomReview[];
    unavailable_dates: string[];
};

export type RoomTypeOption = { value: string; label: string };

export type RoomFilters = {
    q: string | null;
    type: string | null;
    min_price: string | number | null;
    max_price: string | number | null;
    capacity: string | number | null;
    floor: string | number | null;
    amenities: string[];
    sort: string;
};

export type BookingStatus = 'pending' | 'confirmed' | 'cancelled' | 'completed';

export type BookingRoomSummary = {
    id: number;
    name: string;
    slug: string;
    thumbnail: string | null;
    type_label: string;
};

export type Booking = {
    id: number;
    reference: string;
    check_in: string;
    check_out: string;
    nights: number;
    total_price: number;
    status: BookingStatus;
    status_label: string;
    status_color: string;
    notes: string | null;
    created_at: string;
    is_cancellable: boolean;
    can_be_reviewed: boolean;
    room?: BookingRoomSummary;
    user?: { id: number; name: string; email: string };
};

export type Paginated<T> = {
    data: T[];
    links: { url: string | null; label: string; active: boolean }[];
    meta: {
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        from: number | null;
        to: number | null;
    };
};
