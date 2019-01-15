import Axios from 'axios';

const URL_EVENTS = '/secure/event/list';

class EventService {
    static getEvents() {
        return Axios.get(
            URL_EVENTS,
            {
                headers: {
                    'Content-Type': 'application/json',
                }
            }
        );
    }
}

export default EventService;