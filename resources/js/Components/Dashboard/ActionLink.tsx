export default function ActionLink({ href, label }: { href: string; label: string }) {
    return <a href={href} className="text-sm font-medium text-blue-600 hover:text-blue-800">{label}</a>;
}
