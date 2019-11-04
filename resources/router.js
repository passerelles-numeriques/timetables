import Router from 'vue-router';
import Calendar from './pages/Calendar';
import Print from './pages/Print';
import Login from './pages/Login';
import Statistics from './pages/admin/Statistics';
import Modules from './pages/admin/Modules';
import Lessons from './pages/admin/Lessons';
import Teachers from './pages/admin/Teachers';
import Classes from './pages/admin/Classes';
import Rooms from './pages/admin/Rooms';

//As order of execution is not guaranteed
export function buildRouter(baseUrl) {
    console.log('[Vue-router] Setting base URL=' + baseUrl);

    // Define the routes of the application
    let router = new Router({
        base: baseUrl,
        mode: 'history',
        routes: [
        {
            path: '/',
            name: 'Calendar Default',
            component: Calendar
        },
        {
            // Type should be students, rooms, teachers
            path: '/calendar/:type',
            name: 'Calendar',
            component: Calendar
        },
        {
            // Type should be classes, rooms, teachers
            path: '/print/:type',
            name: 'Print',
            component: Print
        },
        {
            path: '/login/',
            name: 'Login',
            component: Login
        },
        {
            meta: { requiresAuth: true },  // This page require to be authenticated
            path: '/admin/stats/',
            name: 'Statistics',
            component: Statistics
        },
        {
            meta: { requiresAuth: true },  // This page require to be authenticated
            path: '/admin/modules/',
            name: 'Modules',
            component: Modules
        },
        {
            meta: { requiresAuth: true },  // This page require to be authenticated
            path: '/admin/lessons/',
            name: 'Lessons',
            component: Lessons
        },
        {
            meta: { requiresAuth: true },  // This page require to be authenticated
            path: '/admin/teachers/',
            name: 'Teachers',
            component: Teachers
        },
        {
            meta: { requiresAuth: true },  // This page require to be authenticated
            path: '/admin/rooms/',
            name: 'Rooms',
            component: Rooms
        },
        {
            meta: { requiresAuth: true },  // This page require to be authenticated
            path: '/admin/classes/',
            name: 'Classes',
            component: Classes
        }
        ]
    });
    
    router.beforeEach(
        (to, from, next) => {
        // Middleware that force to login before mounting admin pages
        if (to.matched.some(record => record.meta.requiresAuth)) {
            // if route requires auth and user isn't authenticated
            if (sessionStorage.getItem("username") == null) {
            let query = to.fullPath.match(/^\/$/) ? {} : { redirect: to.path }
            next(
                {
                path: '/login/',
                query: query
                }
            );
            return;
            }
        } else {
            //Open the application for the first time 
            // => redirect to teachers' calendar
            // or last visited page
            if (to.path == '/' && sessionStorage.length == 0) {
                if (localStorage.getItem('lastVisitedPage') == null
                    || localStorage.getItem('lastVisitedPage') == '/'
                    || localStorage.getItem('lastVisitedPage') == sessionStorage.getItem("pathURL")) {
                    next({path: '/calendar/teachers/'});
                } else {
                    next({path: localStorage.getItem('lastVisitedPage')});
                }
                return;
            }
        }
        //Memorize last visited page
        localStorage.setItem('lastVisitedPage', to.path);
        next()
        }
    );
    
    return router;
}
