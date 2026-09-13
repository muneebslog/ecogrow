import { Link } from '@inertiajs/react';
import { Camera, LayoutGrid, Medal } from 'lucide-react';
import AppLogo from '@/components/app-logo';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { create as diagnosisCreate } from '@/routes/diagnoses';
import { index as rewardsIndex } from '@/routes/rewards';
import type { NavItem } from '@/types';

const mainNavItems: NavItem[] = [
    {
        title: 'Garden',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Diagnose',
        href: diagnosisCreate(),
        icon: Camera,
    },
    {
        title: 'Rewards',
        href: rewardsIndex(),
        icon: Medal,
    },
];

export function AppSidebar() {
    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href={dashboard()} prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={mainNavItems} />
            </SidebarContent>

            <SidebarFooter>
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
